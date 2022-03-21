<?php

declare(strict_types=1);

namespace App\Domain\Stats;



interface IStatsRepository
{

    /** 
     * Sets Time span of statistics
     * @throws IncorrectDateFormatException
     */
    public function setTimeSpan(string $from = 'month ago', string $to = 'now'): IStatsRepository;

    /** Statistics for all Users */
    public function getAllUsersStats(): Stats;

    /** Statistics for specific User */
    public function getUserStats(int $id): Stats;

    /** Statistics for all Rooms */
    public function getAllRoomsStats(): Stats;

    /** Statistics fot specific Room */
    public function getRoomStats(int $id): Stats;

    /** Statistics for all Buildings */
    public function getAllBuildingsStats(int $addressId): Stats;

    /** Statistics for specific Building */
    public function getBuildingStats(int $id): Stats;

    /** Statistics of successfull requests on each endpoint of the API */
    public function getRequestsStats(): Stats;
}
