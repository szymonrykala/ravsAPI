<?php

declare(strict_types=1);

namespace App\Infrastructure\Repository;

use App\Domain\Model\Model;
use App\Domain\Reservation\IReservationRepository;
use App\Domain\Reservation\Reservation;
use App\Domain\Reservation\StartedReservationDeleteException;
use App\Domain\Room\IRoomRepository;
use App\Domain\User\IUserRepository;

use App\Utils\JsonDateTime;
use Psr\Container\ContainerInterface;

final class ReservationRepository extends BaseRepository implements IReservationRepository
{

    /** {@inheritDoc} */
    protected string $table = '`reservation`';

    public function __construct(
        ContainerInterface $di,
        private IRoomRepository $roomRepository,
        private IUserRepository $userRepository
    ) {
        parent::__construct($di);
    }


    /**
     * Construct reservation object from database data 
     */
    protected function newItem(array $data): Reservation
    {
        $user = $this->userRepository->byId((int) $data['user']);
        $room = $this->roomRepository->byId((int)$data['room']);

        $actualStart = $data['actual_start'] ? new JsonDateTime($data['actual_start']) : NULL;
        $actualEnd = $data['actual_end'] ? new JsonDateTime($data['actual_end']) : NULL;

        return new Reservation(
            (int)   $data['id'],
            $data['title'],
            $data['description'],
            $room,
            $user,
            new JsonDateTime($data['planned_start']),
            new JsonDateTime($data['planned_end']),
            $actualStart,
            $actualEnd,
            new JsonDateTime($data['created']),
            new JsonDateTime($data['updated'])
        );
    }


    /**
     * {@inheritDoc}
     */
    public function save(Reservation $reservation): void
    {
        $reservation->validate();
        $sql = "UPDATE $this->table SET
                    `title` = :title,
                    `description` = :description,
                    `room` = :room,
                    `planned_start` = :plannedStart,
                    `planned_end` = :plannedEnd,
                    `actual_start` = :actualStart,
                    `actual_end` = :actualEnd
                WHERE `id` = :id";

        $params = [
            ':id' => $reservation->id,
            ':title' => ucfirst($reservation->title),
            ':description' => ucfirst($reservation->description),
            ':room' => $reservation->roomId,
            ':plannedStart' => $reservation->plannedStart,
            ':plannedEnd' => $reservation->plannedEnd,
            ':actualStart' => $reservation->actualStart,
            ':actualEnd' => $reservation->actualEnd
        ];

        $this->db->query($sql, $params);
    }


    /**
     * {@inheritDoc}
     */
    public function create(
        string      $title,
        string      $description,
        int         $room,
        int         $user,
        JsonDateTime    $plannedStart,
        JsonDateTime    $plannedEnd
    ): int {
        $sql = "INSERT INTO $this->table (`title`, `description`, `room`, `user`, planned_start, planned_end)
                VALUES(:title, :description, :room, :user, :plannedStart, :plannedEnd)";

        $params = [
            ':title' => ucfirst($title),
            ':description' => ucfirst($description),
            ':room' => $room,
            ':user' => $user,
            ':plannedStart' => $plannedStart,
            ':plannedEnd' => $plannedEnd
        ];

        $this->db->query($sql, $params);
        return $this->db->lastInsertId();
    }


    /**
     * {@inheritDoc}
     */
    public function whereBuildingId(int $buildingId): void
    {
        $this->SQL = "SELECT $this->table.* from $this->table
        INNER JOIN `room` ON $this->table.`room` = `room`.`id`";

        $this->SQLwhere .= " AND `room`.`building` = :building_id ";
        $this->params['building_id'] = $buildingId;
    }

    /**
     * {@inheritDoc}
     */
    public function whereAddressId(int $addressId): void
    {
        $this->SQL = "SELECT $this->table.* from $this->table
        INNER JOIN `room` ON $this->table.`room` = `room`.`id`
        INNER JOIN `building` ON `room`.`building` = `building`.`id` ";

        $this->SQLwhere .= " AND `building`.`address` = :address_id ";

        $this->params['address_id'] = $addressId;
    }

    /**
     * {@inheritDoc}
     */
    public function whereAddressAndBuilding(int $addressId, int $buildingId): void
    {
        $this->whereAddressId($addressId);

        $this->params['building_id'] = $buildingId;
        $this->SQLwhere .= " AND building.id = :building_id ";
    }

    /**
     * {@inheritDoc}
     */
    public function fromDate(JsonDateTime $date): void
    {
        $this->SQLwhere .= ' AND ((`actual_start` >= :startDate OR `planned_start`>= :startDate ) OR (`actual_start` IS NOT NULL AND `actual_end` IS NULL))';
        $this->params[':startDate'] = $date;
    }


    /**
     * {@inheritDoc}
     */
    public function search(string $phrase): void
    {
        $this->SQLwhere .= ' AND (
            `description` LIKE :searchString
            OR `title` LIKE :searchString
        )';
        $this->params[':searchString'] = '%' . str_replace(' ', '%', $phrase) . '%';
    }


    /**
     * {@inheritDoc}
     */
    public function forUser(int $userId): void
    {
        $this->SQLwhere .= ' AND `user` = :userId';
        $this->params[':userId'] = $userId;
    }


    /**
     * {@inheritDoc}
     */
    public function forRoom(int $roomId): void
    {
        $this->SQLwhere .= ' AND `room` = :roomId';
        $this->params[':roomId'] = $roomId;
    }

    /**
     * {@inheritDoc}
     */
    public function deleteAllFutureUserReservations(int $deletedUserId): void
    {
        $sql = "DELETE FROM $this->table WHERE
                    `planned_start` > NOW()
                    AND `user` = :userId";
        $params = [':userId' => $deletedUserId];
        $this->db->query($sql, $params);
    }

    /**
     * {@inheritDoc}
     * @param Reservation $reservation
     * @throws StartedReservationDeleteException
     */
    public function delete(Model $reservation): void
    {
        if ($reservation->notStarted()) {
            parent::delete($reservation);
            return;
        }

        throw new StartedReservationDeleteException();
    }
}
