<?php

declare(strict_types=1);

namespace App\Infrastructure\Repository;

use App\Domain\Building\IBuildingRepository;
use App\Domain\Room\{
    IRoomRepository,
    Room
};

use App\Domain\Exception\DomainResourceNotFoundException;
use App\Domain\Image\IImageRepository;
use App\Utils\JsonDateTime;
use Psr\Container\ContainerInterface;


final class RoomRepository extends BaseRepository implements IRoomRepository
{
    protected string $table = 'room';
    private bool $buildingLoading = FALSE;

    public function __construct(
        ContainerInterface $di,
        private IImageRepository $imageRepository,
        private IBuildingRepository $buildingRepository
    ) {
        parent::__construct($di);
    }

    /**
     * {@inheritDoc}
     */
    public function withBuilding(): IRoomRepository
    {
        $this->buildingLoading = TRUE;
        return $this;
    }


    /**
     * @param array $data from database
     * @return Room
     */
    protected function newItem(array $data): Room
    {
        $image = $this->imageRepository->byId((int) $data['image']);
        $building = $this->buildingLoading ? $this->buildingRepository->byId((int) $data['building']) : NULL;
        return new Room(
            (int)   $data['id'],
            $data['name'],
            $image,
            $building,
            $data['rfid'],
            $data['room_type'],
            (int)   $data['seats_count'],
            (int)   $data['floor'],
            (bool)  $data['blocked'],
            (bool)  $data['occupied'],
            new JsonDateTime($data['created']),
            new JsonDateTime($data['updated']),
            (int)   $data['image'],
            (int)   $data['building']
        );
    }

    public function byIdAndBuildingId(int $roomId, int $buildingId): Room
    {
        $sql = "SELECT * FROM `$this->table` WHERE `id` = :id AND `building` = :buildingId";
        $params = [':id' => $roomId, ':buildingId' => $buildingId];
        $result = $this->db->query($sql, $params);
        $roomData = array_pop($result);

        if (empty($roomData)) {
            throw new DomainResourceNotFoundException();
        }

        return $this->newItem($roomData);
    }

    /**
     * @param Room room
     */
    public function save(Room $room): void
    {
        $room->validate();
        $sql = "UPDATE `$this->table` SET
                    `name` = :name,
                    `image` = :imageId,
                    `building` = :buildingId,
                    `rfid` = :rfid,
                    `room_type` = :roomType,
                    `seats_count` = :seatsCount,
                    `floor` = :floor,
                    `blocked` = :blocked,
                    `occupied` = :occupied
                WHERE `id` = :id";

        $params = [
            ':id' => $room->id,
            ':name' => $room->name,
            ':imageId' => $room->imageId,
            ':buildingId' => $room->buildingId,
            ':rfid' => $room->rfid,
            ':roomType' => $room->roomType,
            ':seatsCount' => $room->seatsCount,
            ':floor' => $room->floor,
            ':blocked' => (int) $room->blocked,
            ':occupied' => (int) $room->occupied,
        ];

        $this->db->query($sql, $params);
    }

    /**
     * @param string  name
     * @param int     buildingId
     * @param string  roomType
     * @param int     seatsCount
     * @param int     floor
     */
    public function create(
        string  $name,
        int     $buildingId,
        string  $roomType,
        int     $seatsCount,
        int     $floor
    ): int {
        $sql = "INSERT `$this->table`(name, building, room_type, seats_count, floor)
                VALUES(:name, :buildingId, :roomType, :seatsCount, :floor)";

        $params =  [
            ':name' => $name,
            ':buildingId' => $buildingId,
            ':roomType' => $roomType,
            ':seatsCount' => $seatsCount,
            ':floor' => $floor,
        ];
        $this->db->query($sql, $params);
        return $this->db->lastInsertId();
    }
}
