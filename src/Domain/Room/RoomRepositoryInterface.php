<?php
declare(strict_types=1);

namespace App\Domain\Room;

use App\Domain\Model\RepositoryInterface;



interface RoomRepositoryInterface extends RepositoryInterface
{

    /**
     * enable building loading
     * @return RoomRepositoryInterfave
     */
    public function withBuilding(): RoomRepositoryInterface;

    /**
     * @param Room room
     */
    public function save(Room $room ): void;

    /**
     * @param string name
     * @param int buildingId
     * @param string roomType
     * @param int seatsCount
     * @param int floor
     */
    public function create(
        string  $name,
        int     $buildingId,
        string  $roomType,
        int     $seatsCount,
        int     $floor
    ): int;
}