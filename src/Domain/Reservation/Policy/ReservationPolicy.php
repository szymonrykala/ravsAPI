<?php

declare(strict_types=1);

namespace App\Domain\Reservation\Policy;



use App\Domain\Building\Building;
use App\Domain\Building\IBuildingRepository;
use App\Domain\Exception\DomainBadRequestException;
use App\Domain\Room\Room;
use App\Domain\Room\RoomRepositoryInterface;
use App\Domain\User\User;
use App\Domain\User\UserRepositoryInterface;
use App\Infrastructure\Database\IDatabase;
use DateTime;
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

    protected stdClass $form;


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
     */
    abstract function __invoke(stdClass $form): void;

    /**
     * check if the room is blocked
     * @throws BlockedRoomException
     */
    protected function checkRoom(): void
    {
        if ($this->room->buildingId !== $this->building->id)
            throw new RoomBuildingConflictException(
                $this->room->id,
                $this->building->id
            );

        if ($this->room->blocked)
            throw new BlockedRoomException();
    }

    protected function checkTimePolicies(?int $excludeSelfId = NULL): void
    {
        $this->checkTimeSlot();
        $this->checkBuildingOpeningHours();
        $this->checkSimilarReservationTimeSlot($excludeSelfId);
    }

    /**
     * check if the building is open ig given time slot
     * @throws TimeSlotConflictException
     * @return void
     */
    private function checkBuildingOpeningHours(): void
    {

        $startTimeCorrect = $this->form->plannedStart > $this->building->openTime;
        $endTimeCorrect = $this->form->plannedEnd < $this->building->closeTime;

        if (!$startTimeCorrect && !$endTimeCorrect)
            throw new TimeSlotConflictException(
                'Building opening hours are '
                    . $this->building->openTime->format('H:i:s')
                    . ' to ' . $this->building->closeTime->format('H:i:s')
                    . '. Please, change reservation time slot.'
            );
    }


    /**
     * check if the time slot is correct
     * @throws IncorrectTimeSlotException
     * @return void
     */
    private function checkTimeSlot(): void
    {
        /** @var DateInterval $timeDiff*/
        $timeDiff = $this->form->plannedStart->diff($this->form->plannedEnd);

        if ($timeDiff->invert === 1)
            throw new IncorrectTimeSlotException(
                'Incorrect time slot: plannedStart have to be earlier then plannedEnd'
            );

        // 0 days, 0 months, 0 years, max time is 12:59:59
        $correctTimeSpan = $timeDiff->h <= 12
            && $timeDiff->d === 0
            && $timeDiff->m === 0
            && $timeDiff->y === 0;

        if (!$correctTimeSpan)
            throw new IncorrectTimeSlotException(
                "Reservation time slot is too big. Maximum time is 12 hours."
            );
    }


    /** Check if reservation with similar time slot exists
     * @throws TimeSlotConflictException
     * @return void
     */
    private function checkSimilarReservationTimeSlot(?int $excludeId = NULL): void
    {
        $sql = 'SELECT `id` from `reservation` WHERE '
            .( $excludeId ? '`id` ~= :reservationId AND ':'' ).
            '`room` = :roomId 
                    AND (`planned_start` BETWEEN :plannedStart AND :plannedEnd
                    OR `planned_end` BETWEEN :plannedStart AND :plannedEnd)
         ';

        $params = [
            ':roomId' => $this->room->id,
            ':plannedStart' => $this->form->plannedStart->format('c'),
            ':plannedEnd' => $this->form->plannedEnd->format('c')
        ];

        if ($excludeId) $params[':reservationId'] = $excludeId;

        $result = $this->database->query($sql, $params);

        if (!empty($result)) throw new TimeSlotConflictException();
    }
}
