<?php

declare(strict_types=1);

namespace App\Domain\Reservation;

use App\Domain\Model\IRepository;
use App\Utils\Pagination;
use App\Utils\JsonDateTime;


interface IReservationRepository extends IRepository
{
    /**
     * Saves reservation object state
     */
    public function save(Reservation $reservation): void;

    /**
     * Creates new reservation object
     */
    public function create(
        string      $title,
        string      $description,
        int         $room,
        int         $user,
        JsonDateTime    $planned_start,
        JsonDateTime    $planned_end
    ): int;

    /**
     * @param int $deletedUserId
     */
    public function deleteAllFutureUserReservations(int $deletedUserId): void;

    /**
     * Read reservation from given date
     */
    public function fromDate(JsonDateTime $date): void;

    /**
     * Search given phrase in title and description of reservations
     */
    public function search(string $stringToSearch): void;

    /**
     * Read reservations from specific building
     */
    public function whereBuildingId(int $buildingId): void;

    /**
     * Read reservations from specific address
     */
    public function whereAddressId(int $addressId): void;

    /**
     * Read reservations from specific address and building
     */
    public function whereAddressAndBuilding(int $addressId, int $buildingId): void;

    /**
     * Read reservation for specific user
     */
    public function forUser(int $userId): void;

    /**
     * Read reservations for specific room
     */
    public function forRoom(int $roomId): void;
}
