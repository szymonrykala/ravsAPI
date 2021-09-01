<?php
declare(strict_types=1);

namespace App\Domain\Room;

use App\Domain\Model\RepositoryInterface;



interface RoomRepositoryInterface extends RepositoryInterface
{
    /**
     * @param int id
     */
    public function deleteById(int $id): void;
    
    /**
     * @param Room room
     */
    public function save(Room $room ): void;

    /**
     * @param string name
     * @param int imageId
     * @param int addressId
     */
    public function create(
        string  $name,
        int     $buildingId,
        string  $roomType,
        int     $seatsCount,
        int     $floor
    ): int;
}