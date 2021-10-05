<?php

declare(strict_types=1);

namespace App\Infrastructure\Repository;

use App\Infrastructure\Database\IDatabase;

use App\Domain\Stats\{
    IncorrectDateFormatException,
    IStatsRepository,
    Stats,
    StatsItem
};
use App\Utils\JsonDateTime;
use DateInterval;



class StatsRepository implements IStatsRepository
{
    private IDatabase $db;
    private Stats $stats;
    private string $between;
    private array $params;

    private string $reservations = 'reservation';
    private string $rooms = 'room';
    private string $buildings = 'building';
    private string $users = 'user';
    private string $requests = 'request';


    private string $avgTimeFileds = "SEC_TO_TIME(AVG(TIME_TO_SEC(TIMEDIFF(res.planned_end, res.planned_start)))) as 'averagePlannedTime',
        SEC_TO_TIME(AVG(TIME_TO_SEC(TIMEDIFF(res.actual_end, res.actual_start)))) as 'averageActualTime'";


    public function __construct(
        IDatabase $db
    ) {
        $this->db = $db;
        $this->db->connect();

        $this->stats = new Stats();
        $this->setTimeSpan();
    }

    private function setBetween(JsonDateTime $from, JsonDateTime $to): void
    {
        $this->between = " `planned_start` BETWEEN DATE(:from) AND DATE(:to)";
        $this->params[':from'] = $from->getDate();
        $this->params[':to'] = $to->getDate();
    }

    private function execute(string $sql = ''): array
    {
        return $this->db->query($sql, $this->params);
    }

    /** {@inheritDoc} */
    public function setTimeSpan(?string $from = NULL, string $to = 'now'): IStatsRepository
    {
        if ($from === NULL) {
            $date = new JsonDateTime('now');
            $oneMonth = new DateInterval('P1M');
            $oneMonth->invert = -1; // one month ago
            $from = $date->add($oneMonth)->getDate();
        }

        try {
            $this->setBetween(
                new JsonDateTime($from),
                new JsonDateTime($to)
            );
        } catch (\Exception $e) {
            // print_r($e->getMessage());
            throw new IncorrectDateFormatException();
        }

        return $this;
    }

    /** {@inheritDoc} */
    public function getAllRoomsStats(): Stats
    {
        $sql = "SELECT r.id, 
                        r.name,
                        COUNT(res.id) as 'reservationsCount',
                        {$this->avgTimeFileds}
                    FROM {$this->rooms} r join {$this->reservations} res ON res.room = r.id 
                    WHERE {$this->between}
                    GROUP BY r.id 
                    ORDER BY r.id";

        $this->stats->addStatsItem(new StatsItem(
            'allRoomsReservations',
            $this->execute($sql)
        ));

        return $this->stats;
    }


    /** {@inheritDoc} */
    public function getRoomStats(int $id): Stats
    {
        $this->params[':roomId'] = $id;

        // Reservations for specific room groupped by day of week
        {
            $sql = "SELECT COUNT(res.id) as 'reservationsCount',
                            DAYOFWEEK (res.planned_start) AS 'dayOfWeek',
                            {$this->avgTimeFileds}
                        FROM {$this->rooms} r 
                            INNER JOIN {$this->reservations} res ON res.room = r.id
                        WHERE r.id = :roomId AND {$this->between}
                        GROUP BY dayOfWeek
                        ORDER BY dayOfWeek";



            $this->stats->addStatsItem(new StatsItem(
                'dayOfWeek',
                $this->execute($sql)
            ));
        }
        // Reservations for specific room groupped by day of month
        {
            $sql = "SELECT COUNT(res.id) as 'reservationsCount',
                            DAYOFMONTH (res.planned_start) AS 'dayOfMonth',
                            {$this->avgTimeFileds}
                        FROM `{$this->rooms}` r 
                            INNER JOIN `{$this->reservations}` res ON res.room = r.id
                        WHERE r.id = :roomId AND {$this->between}
                        GROUP BY dayOfMonth
                        ORDER BY dayOfMonth";


            $this->stats->addStatsItem(new StatsItem(
                'dayOfMonth',
                $this->execute($sql)
            ));
        }
        // Users who makes reservation for specific room
        {
            $sql = "SELECT u.id, 
                            u.email, 
                            COUNT(res.id) as 'reservationsCount', 
                            {$this->avgTimeFileds}
                        FROM `{$this->rooms}` r 
                            INNER JOIN `{$this->reservations}` res ON res.room = r.id 
                            INNER JOIN `{$this->users}` u ON res.user = u.id
                        WHERE r.id = :roomId AND {$this->between}
                        GROUP BY  u.email 
                        ORDER BY reservationsCount";


            $this->stats->addStatsItem(new StatsItem(
                'users',
                $this->execute($sql)
            ));
        }

        return $this->stats;
    }


    /** {@inheritDoc} */
    public function getAllUsersStats(): Stats
    {
        $sql = "SELECT u.id, 
                        u.email, 
                        COUNT(res.id) as 'reservationsCount',
                        {$this->avgTimeFileds}
                    FROM {$this->reservations} res 
                        INNER JOIN {$this->users} u ON res.user = u.id 
                    WHERE {$this->between}
                    GROUP BY res.user 
                    ORDER BY reservationsCount";

        $this->stats->addStatsItem(new StatsItem(
            'allUsers',
            $this->execute($sql)
        ));

        return $this->stats;
    }


    /** {@inheritDoc} */
    public function getUserStats(int $id): Stats
    {
        $this->params[':userId'] = $id;
        // Reservations of specific User groupped by day of week
        {
            $sql = "SELECT 
                            COUNT(res.id) as 'reservationsCount', 
                            DAYOFWEEK (res.planned_start) AS 'dayOfWeek', 
                            {$this->avgTimeFileds}
                        FROM `{$this->users}` u 
                            INNER JOIN `{$this->reservations}` res ON res.user = u.id 
                        WHERE u.id = :userId AND {$this->between}
                        GROUP BY dayOfWeek
                        ORDER BY dayOfWeek";


            $this->stats->addStatsItem(new StatsItem(
                'dayOfWeek',
                $this->execute($sql)
            ));
        }
        // Reservations of specific room groupped by day of month
        {
            $sql = "SELECT 
                            COUNT(res.id) as 'reservationsCount',
                            DAYOFMONTH(res.planned_start) AS 'dayOfMonth',
                            {$this->avgTimeFileds}
                        FROM `{$this->users}` u 
                            INNER JOIN `{$this->reservations}` res ON res.user = u.id 
                        WHERE u.id = :userId AND {$this->between}
                        GROUP BY dayOfMonth
                        ORDER BY dayOfMonth";

            $this->stats->addStatsItem(new StatsItem(
                'dayOfMonth',
                $this->execute($sql)
            ));
        }
        // Rooms reserved by specific User
        {
            $sql = "SELECT 
                            COUNT(res.id) as 'reservationsCount', 
                            r.name as 'roomName',
                            b.name as 'buildingName',
                            {$this->avgTimeFileds}
                        FROM `{$this->users}` u 
                            INNER JOIN `{$this->reservations}` res ON res.user = u.id 
                            INNER JOIN `{$this->rooms}` r  ON res.room = r.id 
                            INNER JOIN `{$this->buildings}` b ON r.building = b.id
                        WHERE u.id = :userId AND {$this->between}
                        GROUP BY res.room
                        ORDER BY res.room";


            $this->stats->addStatsItem(new StatsItem(
                'reservedRooms',
                $this->execute($sql)
            ));
        }

        return $this->stats;
    }

    /** {@inheritDoc} */
    public function getAllBuildingsStats(): Stats
    {
        $sql = "SELECT b.id, 
                        b.name,
                        COUNT(res.id) as 'reservationsCount',
                        {$this->avgTimeFileds}
                    FROM `{$this->reservations}` res 
                        INNER JOIN `{$this->rooms}` r ON r.id = res.room
                        INNER JOIN `{$this->buildings}` b ON r.building = b.id
                    WHERE {$this->between}
                    GROUP BY b.id 
                    ORDER BY b.id";

        $this->stats->addStatsItem(new StatsItem(
            'allReservations',
            $this->execute($sql)
        ));

        return $this->stats;
    }


    /** {@inheritDoc} */
    public function getBuildingStats(int $id): Stats
    {
        $this->params[':buildingId'] = $id;
        // Reservations for specific room groupped by day of week
        {
            $sql = "SELECT COUNT(res.id) as 'reservationsCount',
                            DAYOFWEEK (res.planned_start) AS 'dayOfWeek',
                            {$this->avgTimeFileds}
                        FROM `{$this->reservations}` res 
                            INNER JOIN `{$this->rooms}` r ON r.id = res.room
                            INNER JOIN `{$this->buildings}` b ON r.building = b.id
                        WHERE b.id = :buildingId AND {$this->between}
                        GROUP BY dayOfWeek";



            $this->stats->addStatsItem(new StatsItem(
                'dayOfWeek',
                $this->execute($sql)
            ));
        }
        // Reservations for specific room groupped by day of month
        {
            $sql = "SELECT COUNT(res.id) as 'reservationsCount',
                            DAYOFMONTH (res.planned_start) AS 'dayOfMonth',
                            {$this->avgTimeFileds}
                        FROM `{$this->reservations}` res 
                            INNER JOIN `{$this->rooms}` r ON r.id = res.room
                            INNER JOIN `{$this->buildings}` b ON r.building = b.id
                        WHERE b.id = :buildingId AND {$this->between}
                        GROUP BY dayOfMonth";


            $this->stats->addStatsItem(new StatsItem(
                'dayOfMonth',
                $this->execute($sql)
            ));
        }
        // Users who makes reservation for specific building
        {
            $sql = "SELECT u.id, 
                            u.email, 
                            COUNT(res.id) as 'reservationsCount', 
                            {$this->avgTimeFileds}
                        FROM `{$this->reservations}` res 
                            INNER JOIN `{$this->rooms}` r ON r.id = res.room
                            INNER JOIN `{$this->buildings}` b ON r.building = b.id
                            INNER JOIN `{$this->users}` u ON res.user = u.id
                        WHERE b.id = :buildingId AND {$this->between}
                        GROUP BY  u.email 
                        ORDER BY reservationsCount";


            $this->stats->addStatsItem(new StatsItem(
                'users',
                $this->execute($sql)
            ));
        }

        return $this->stats;
    }


    /** {@inheritDoc} */
    public function getRequestsStats(): Stats
    {

        $sql = "SELECT 
                    method, 
                    COUNT(id) as calls,
                    REGEXP_REPLACE(endpoint, '\\\d+', 'id') as 'generalEndpoint' , 
                    AVG(time) as avgTime,
                    SUM(time) as timeForEndpoint
                FROM {$this->requests} 
                WHERE `created` BETWEEN DATE(:from) AND DATE(:to)
                GROUP BY generalEndpoint, method 
                ORDER BY generalEndpoint ASC
        ";

        $this->stats->addStatsItem(new StatsItem(
            'endpoints',
            $this->execute($sql)
        ));

        return $this->stats;
    }
}
