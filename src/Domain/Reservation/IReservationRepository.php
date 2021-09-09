<?php

declare(strict_types=1);

namespace App\Domain\Reservation;

use App\Domain\Model\RepositoryInterface;
use App\Utils\Pagination;
use DateTime;


interface IReservationRepository extends RepositoryInterface
{
    /**
     * @param Reservation reservation
     * @return void
     */
    public function save(Reservation $reservation): void;

    /**
     * @param string      title,
     * @param string      description,
     * @param int         room,
     * @param int         user,
     * @param DateTime    planned_start,
     * @param DateTime    planned_end,
     * @return int
     */
    public function create(
        string      $title,
        string      $description,
        int         $room,
        int         $user,
        DateTime    $planned_start,
        DateTime    $planned_end
    ): int;

    /**
     * @param Pagination data
     * @return int pagesCount
     */
    public function setPagination(Pagination $pagination): void;

    /**
     * @return Pagination
     */
    public function getPagination(): Pagination;

    /**
     * @param DateTime date
     * @return void
     */
    public function withDate(DateTime $date): void;

    /**
     * @param DateTime date
     * @return void
     */
    public function withCreateDate(DateTime $date): void;

    /**
     * @return void
     */
    public function like(string $stringToSearch): void;

    /**
     * @param int userId
     * @return void
     */
    public function forUser(int $userId): void;

    /**
     * @param int userId
     * @return void
     */
    public function confirmedByUser(int $userId): void;

    /**
     * @param int roomId
     * @return void
     */
    public function forRoom(int $roomId): void;

}
