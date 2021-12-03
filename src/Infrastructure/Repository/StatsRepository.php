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



final class StatsRepository implements IStatsRepository
{
    private Stats $stats;
    private string $between;
    private array $params;

    private string $reservations = 'reservation';
    private string $rooms = 'room';
    private string $buildings = 'building';
    private string $users = 'user';
    private string $requests = 'request';


    private string $avgTimeFileds = "ROUND(AVG(TIME_TO_SEC(TIMEDIFF(res.planned_end, res.planned_start)))/60) as 'avgPlannedTimeMinutes',
        ROUND(AVG(TIME_TO_SEC(TIMEDIFF(res.actual_end, res.actual_start)))/60) as 'avgActualTimeMinutes',
        SUM(ROUND(TIME_TO_SEC(TIMEDIFF(res.actual_end, res.actual_start))/60)) as 'allTimeMinutes'";


    public function __construct(
        private IDatabase $db
    ) {
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


    /**
     * parse values to integers excluding keys contains in $excludeKeys array
     */
    private function parseToNumbers(&$data, array $excludeKeys = [])
    {
        foreach ($data as &$obj) {
            foreach ($obj as $key => &$value) {
                if (!in_array($key, $excludeKeys)) $value = (float) $value;
            }
        }
    }

    /**
     * fills data for data per day of week
     */
    private function fillDaysOfWeek(array $data): array
    {
        $temp = [];
        $filledData = [];

        foreach ($data as &$obj) $temp[$obj['day']] = $obj;

        for ($i = 1; $i < 7; $i++) {
            if (isset($temp[$i])) {
                $tempObj = $temp[$i];

                array_push($filledData, $tempObj);
            } else {
                array_push($filledData, [
                    'reservationsCount' => 0,
                    'avgActualTimeMinutes' => 0,
                    'avgPlannedTimeMinutes' => 0,
                    'allTimeMinutes' => 0,
                    'day' => $i,
                ]);
            }
        }
        return $filledData;
    }

    /**
     * fills data for data per day of month
     */
    private function fillDaysOfMonth(array $data): array
    {
        $temp = [];
        $filledData = [];

        foreach ($data as $obj) $temp[$obj['day']] = $obj;


        for ($i = 1; $i <= 32; $i++) {
            $item = isset($temp[$i]) ? $temp[$i] : [
                "reservationsCount" => 0,
                "day" => $i,
                "avgPlannedTimeMinutes" => 0,
                "avgActualTimeMinutes" => 0,
                "allTimeMinutes" => 0
            ];

            array_push($filledData, $item);
        }
        return $filledData;
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

        $data =  $this->execute($sql);
        $this->parseToNumbers($data, ['name']);

        $this->stats->addStatsItem(new StatsItem(
            'allReservations',
            $data
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
                            DAYOFWEEK (res.planned_start) AS 'day',
                            {$this->avgTimeFileds}
                        FROM {$this->rooms} r 
                            INNER JOIN {$this->reservations} res ON res.room = r.id
                        WHERE r.id = :roomId AND {$this->between}
                        GROUP BY day
                        ORDER BY day";

            $data = $this->execute($sql);
            $this->parseToNumbers($data);

            $filledData = $this->fillDaysOfWeek($data);

            $this->stats->addStatsItem(new StatsItem(
                'weekly',
                $filledData
            ));
        }
        // Reservations for specific room groupped by day of month
        {
            $sql = "SELECT COUNT(res.id) as 'reservationsCount',
                            DAYOFMONTH (res.planned_start) AS 'day',
                            {$this->avgTimeFileds}
                        FROM `{$this->rooms}` r 
                            INNER JOIN `{$this->reservations}` res ON res.room = r.id
                        WHERE r.id = :roomId AND {$this->between}
                        GROUP BY day
                        ORDER BY day";

            $data = $this->execute($sql);
            $this->parseToNumbers($data);


            $this->stats->addStatsItem(new StatsItem(
                'monthly',
                $this->fillDaysOfMonth($data)
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

            $data = $this->execute($sql);
            $this->parseToNumbers($data, ['email']);


            $this->stats->addStatsItem(new StatsItem(
                'users',
                $data
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
                    WHERE u.deleted = 0 AND {$this->between} 
                    GROUP BY res.user 
                    ORDER BY reservationsCount";

        $data =  $this->execute($sql);
        $this->parseToNumbers($data, ['email']);

        $this->stats->addStatsItem(new StatsItem(
            'users',
            $data
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
                            DAYOFWEEK (res.planned_start) AS 'day', 
                            {$this->avgTimeFileds}
                        FROM `{$this->users}` u 
                            INNER JOIN `{$this->reservations}` res ON res.user = u.id 
                        WHERE u.id = :userId AND {$this->between}
                        GROUP BY day
                        ORDER BY day";

            $data =  $this->execute($sql);
            $this->parseToNumbers($data);

            $this->stats->addStatsItem(new StatsItem(
                'weekly',
                $this->fillDaysOfWeek($data)
            ));
        }
        // Reservations of specific room groupped by day of month
        {
            $sql = "SELECT 
                            COUNT(res.id) as 'reservationsCount',
                            DAYOFMONTH(res.planned_start) AS 'day',
                            {$this->avgTimeFileds}
                        FROM `{$this->users}` u 
                            INNER JOIN `{$this->reservations}` res ON res.user = u.id 
                        WHERE u.id = :userId AND {$this->between}
                        GROUP BY day
                        ORDER BY day";

            $data =  $this->execute($sql);
            $this->parseToNumbers($data);

            $this->stats->addStatsItem(new StatsItem(
                'monthly',
                $this->fillDaysOfMonth($data)
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

            $data =  $this->execute($sql);
            $this->parseToNumbers($data, ['roomName', 'buildingName']);

            $this->stats->addStatsItem(new StatsItem(
                'reservedRooms',
                $data
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

        $data =  $this->execute($sql);
        $this->parseToNumbers($data, ['name']);

        $this->stats->addStatsItem(new StatsItem(
            'allReservations',
            $data
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
                            DAYOFWEEK (res.planned_start) AS 'day',
                            {$this->avgTimeFileds}
                        FROM `{$this->reservations}` res 
                            INNER JOIN `{$this->rooms}` r ON r.id = res.room
                            INNER JOIN `{$this->buildings}` b ON r.building = b.id
                        WHERE b.id = :buildingId AND {$this->between}
                        GROUP BY day";

            $data = $this->execute($sql);
            $this->parseToNumbers($data);

            $this->stats->addStatsItem(new StatsItem(
                'weekly',
                $this->fillDaysOfWeek($data)
            ));
        }
        // Reservations for specific room groupped by day of month
        {
            $sql = "SELECT COUNT(res.id) as 'reservationsCount',
                            DAYOFMONTH (res.planned_start) AS 'day',
                            {$this->avgTimeFileds}
                        FROM `{$this->reservations}` res 
                            INNER JOIN `{$this->rooms}` r ON r.id = res.room
                            INNER JOIN `{$this->buildings}` b ON r.building = b.id
                        WHERE b.id = :buildingId AND {$this->between}
                        GROUP BY day";
            $data =  $this->execute($sql);
            $this->parseToNumbers($data);

            $this->stats->addStatsItem(new StatsItem(
                'monthly',
                $this->fillDaysOfMonth($data)
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

            $data =  $this->execute($sql);
            $this->parseToNumbers($data, ['email']);

            $this->stats->addStatsItem(new StatsItem(
                'users',
                $data
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
                    REGEXP_REPLACE(endpoint, '/\\\d+', '/id') as 'generalEndpoint' , 
                    AVG(time) as avgTime,
                    SUM(time) as timeForEndpoint
                FROM {$this->requests} 
                GROUP BY generalEndpoint, method 
                ORDER BY generalEndpoint ASC
        ";
        $this->params = [];

        $data =  $this->execute($sql);
        $this->parseToNumbers($data, ['method', 'generalEndpoint']);


        $this->stats->addStatsItem(new StatsItem(
            'endpoints',
            $data
        ));

        return $this->stats;
    }
}
