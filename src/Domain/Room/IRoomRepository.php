<?php
declare(strict_types=1);

namespace App\Domain\Room;

use App\Domain\Model\IRepository;



interface IRoomRepository extends IRepository
{

    /**
     * enable building loading
     * @return RoomRepositoryInterfave
     */
    public function withBuilding(): IRoomRepository;

    /**
     * Saves room state
     */
    public function save(Room $room ): void;

    /** Sets default image for room */
    public function setDefaultImage(Room $room): void;

    /**
     * {@inheritDoc}
     */
    public function create(
        string  $name,
        int     $buildingId,
        string  $roomType,
        int     $seatsCount,
        int     $floor
    ): int;

    /**
     * search room by id and building id
     * @throws DomainResourceNotFoundException
     */
    public function byIdAndBuildingId(int $roomId, int $buildingId): Room;
}