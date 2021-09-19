<?php

declare(strict_types=1);

namespace App\Domain\Reservation\Policy;



use App\Domain\Building\Building;
use App\Domain\Building\IBuildingRepository;
use App\Domain\Reservation\Reservation;
use App\Domain\Room\Room;
use App\Domain\Room\RoomRepositoryInterface;
use App\Domain\User\User;
use App\Domain\User\UserRepositoryInterface;
use App\Infrastructure\Database\IDatabase;
use App\Utils\JsonDateTime;
use DateInterval;
use stdClass;


abstract class ReservationPolicy
{

    protected IDatabase $database;

    protected IBuildingRepository $buildingRepository;
    protected RoomRepositoryInterface $roomRepository;
    protected UserRepositoryInterface $userRepository;

    protected User $user;
    protected Room $room;
    protected Building $building;


    /**
     * @param IBuildingRepository buildingRepository
     * @param RoomRepositoryInterface roomRepository
     * @param UserRepositoryInterface userRepository
     * @param IDatabase database
     */
    public function __construct(
        IBuildingRepository $buildingRepository,
        RoomRepositoryInterface $roomRepository,
        UserRepositoryInterface $userRepository,
        IDatabase $database
    ) {
        $this->database = $database;
        $this->database->connect();

        $this->buildingRepository = $buildingRepository;
        $this->roomRepository = $roomRepository;
        $this->userRepository = $userRepository;
    }

    /**
     * @param stdClass form data
     * @return void
     */
    abstract function __invoke(stdClass $form, ?Reservation $originalReservation = NULL): void;

    /**
     * check if the room is blocked
     * @throws BlockedRoomException
     * @return void
     */
    protected function roomCannotBeBlocked(): void
    {
        if ($this->room->blocked)
            throw new BlockedRoomException();
    }

    /**
     * @throws RoomBuildingConflictException
     * @return void
     */
    protected function roomBelongsToBuilding(): void
    {
        if ($this->room->buildingId !== $this->building->id)
            throw new RoomBuildingConflictException(
                $this->room->id,
                $this->building->id
            );
    }

    /**
     * @throws IncorrectTimeSlotException
     * @return void
     */
    protected function reservationHasFutureTime(): void
    {
        $now = new JsonDateTime('now');

        $futureTime = $this->start > $now;

        if (!$futureTime)
            throw new IncorrectTimeSlotException(
                'The time you want to reserve has already passed.'
            );
    }

    /**
     * @throws IncorrectTimeSlotException
     * @return void
     */
    protected function reservationTimeSlotLengthIsOk(): void
    {
        /** @var DateInterval $timeDiff*/
        $timeDiff = $this->start->diff($this->end);

        if ($timeDiff->invert === 1)
            throw new IncorrectTimeSlotException(
                'Incorrect time slot: \'plannedStart\' have to be earlier then \'plannedEnd\''
            );


        // 0 days, 0 months, 0 years, max time is 12:59:59
        $timeSlotToLong = !($timeDiff->h <= 12
            && $timeDiff->d === 0
            && $timeDiff->m === 0
            && $timeDiff->y === 0);

        // at least 30 minutes
        $timeSlotToShort = !($timeDiff->i >= 30 || $timeDiff->h > 0);

        if ($timeSlotToLong)
            throw new IncorrectTimeSlotException(
                'Reservation time slot is too long - maximum time is 12 hours.'
            );

        if ($timeSlotToShort)
            throw new IncorrectTimeSlotException(
                'Reservation time is to short - at least 30 minutes is required.'
            );
    }

    /**
     * check if the building is open ig given time slot
     * @throws TimeSlotConflictException
     * @return void
     */
    protected function reservationWhenBuildingIsOpen(): void
    {

        $startsAfterOpen = $this->start->getTime() > $this->building->openTime->getTime();
        $endsBeforeClose = $this->end->getTime() < $this->building->closeTime->getTime();

        if (!$startsAfterOpen || !$endsBeforeClose)
            throw new TimeSlotConflictException(
                'Building opening hours are \''
                    . $this->building->openTime->getTime()
                    . '\' to \'' . $this->building->closeTime->getTime()
                    . '\'. Please, change reservation time slot.\''
            );
    }


    /** Check if reservation with similar time slot exists
     * @throws TimeSlotConflictException
     * @return void
     */
    protected function noCrossingReservationWasMade(?int $excludeId = NULL): void
    {
        $sql = 'SELECT `id` from `reservation` WHERE '
            . ($excludeId ? '`id` != :reservationId AND ' : '') . //excluding current reservation while updating
            '`room` = :roomId 
                AND (
                        (
                            `planned_start` BETWEEN :plannedStart AND :plannedEnd
                            OR `planned_end` BETWEEN :plannedStart AND :plannedEnd
                        )
                        OR 
                        (
                            `planned_start` < :plannedStart AND `planned_end` > :plannedEnd
                        )
                    )
         ';

        $params = [
            ':roomId' => $this->room->id,
            ':plannedStart' => $this->start,
            ':plannedEnd' => $this->end
        ];

        if ($excludeId) $params[':reservationId'] = $excludeId;

        $result = $this->database->query($sql, $params);

        if (!empty($result)) throw new TimeSlotConflictException();
    }
}
