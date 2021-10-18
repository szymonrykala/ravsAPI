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
     * {@inheritDoc}
     */
    public function save(Room $room ): void;

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
}