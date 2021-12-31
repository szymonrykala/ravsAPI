<?php

declare(strict_types=1);

namespace App\Infrastructure\Repository;

use App\Infrastructure\Database\IDatabase;

use App\Domain\Stats\{
    IncorrectDateFormatException,
    IStatsRepository,
    LogStatItem,
    PerDayStatItem,
    RoomsOrBuildingReservationsStatItem,
    RoomStatItem,
    Stats,
    StatsArray,
    UserStatItem
};
use App\Utils\JsonDateTime;
use DateInterval;



final class StatsRepository implements IStatsRepository
{
    private Stats $stats;
    private string $between;
    private array $params;

    private string $reservations = '"reservation"';
    private string $rooms = '"room"';
    private string $buildings = '"building"';
    private string $users = '"user"';
    private string $requests = '"request"';


    private string $avgTimeFileds = "
    ROUND(AVG(extract(epoch  from  (select res.planned_end - res.planned_start)))/60) as avg_planned_time_minutes,
    ROUND(AVG(extract(epoch  from  (select res.actual_end - res.actual_start)))/60) as avg_actual_time_minutes,
    SUM(ROUND(extract(epoch  from  (select res.actual_end - res.actual_start))/60)) as all_time_minutes
    ";

    public function __construct(
        private IDatabase $db
    ) {
        $this->db->connect();

        $this->stats = new Stats();
        $this->setTimeSpan();
    }

    private function setBetween(JsonDateTime $from, JsonDateTime $to): void
    {
        $this->between = " planned_start BETWEEN DATE(:from) AND DATE(:to)";
        $this->params[':from'] = $from->getDate();
        $this->params[':to'] = $to->getDate();
    }

    private function execute(string $sql = ''): array
    {
        return $this->db->query($sql, $this->params);
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
                    'reservations_count' => 0,
                    'avg_actual_time_minutes' => 0,
                    'avg_planned_time_minutes' => 0,
                    'all_time_minutes' => 0,
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
                "reservations_count" => 0,
                "day" => $i,
                "avg_planned_time_minutes" => 0,
                "avg_actual_time_minutes" => 0,
                "all_time_minutes" => 0
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
                        COUNT(res.id) as reservations_count,
                        {$this->avgTimeFileds}
                    FROM {$this->rooms} r join {$this->reservations} res ON res.room = r.id 
                    WHERE {$this->between}
                    GROUP BY r.id 
                    ORDER BY r.id";

        $data =  $this->execute($sql);

        $this->stats->addStatsItem(new StatsArray(
            'allReservations',
            array_map(fn ($row) => new RoomsOrBuildingReservationsStatItem(
                (int) $row['id'],
                $row['name'],
                (int) $row['reservations_count'],
                (float) $row['avg_actual_time_minutes'],
                (float) $row['avg_planned_time_minutes'],
                (float) $row['all_time_minutes']
            ), $data)
        ));

        return $this->stats;
    }


    /** {@inheritDoc} */
    public function getRoomStats(int $id): Stats
    {
        $this->params[':roomId'] = $id;

        // Reservations for specific room groupped by day of week
        {
            $sql = "SELECT COUNT(res.id) as reservations_count,
                            date_part('dow', res.planned_start) AS day,
                            {$this->avgTimeFileds}
                        FROM {$this->rooms} r 
                            INNER JOIN {$this->reservations} res ON res.room = r.id
                        WHERE r.id = :roomId AND {$this->between}
                        GROUP BY day
                        ORDER BY day";

            $data = $this->execute($sql);

            $filledData = $this->fillDaysOfWeek($data);

            $this->stats->addStatsItem(new StatsArray(
                'weekly',
                array_map(fn ($row) => new PerDayStatItem(
                    (int) $row['day'],
                    (int) $row['reservations_count'],
                    (float) $row['avg_actual_time_minutes'],
                    (float) $row['avg_planned_time_minutes'],
                    (float) $row['all_time_minutes']
                ), $filledData)
            ));
        }
        // Reservations for specific room groupped by day of month
        {
            $sql = "SELECT COUNT(res.id) as reservations_count,
                            date_part('day', res.planned_start) AS day,
                            {$this->avgTimeFileds}
                        FROM {$this->rooms} r 
                            INNER JOIN {$this->reservations} res ON res.room = r.id
                        WHERE r.id = :roomId AND {$this->between}
                        GROUP BY day
                        ORDER BY day";

            $data = $this->execute($sql);


            $this->stats->addStatsItem(new StatsArray(
                'monthly',
                array_map(fn ($row) => new PerDayStatItem(
                    (int) $row['day'],
                    (int) $row['reservations_count'],
                    (float) $row['avg_actual_time_minutes'],
                    (float) $row['avg_planned_time_minutes'],
                    (float) $row['all_time_minutes']
                ), $this->fillDaysOfMonth($data))
            ));
        }
        // Users who makes reservation for specific room
        {
            $sql = "SELECT u.id, 
                            u.email, 
                            COUNT(res.id) as reservations_count, 
                            {$this->avgTimeFileds}
                        FROM {$this->rooms} r 
                            INNER JOIN {$this->reservations} res ON res.room = r.id 
                            INNER JOIN {$this->users} u ON res.user = u.id
                        WHERE r.id = :roomId AND {$this->between}
                        GROUP BY  u.email, u.id 
                        ORDER BY reservations_count";

            $data = $this->execute($sql);

            $this->stats->addStatsItem(new StatsArray(
                'users',
                array_map(fn ($row) => new UserStatItem(
                    (int) $row['id'],
                    $row['email'],
                    (int) $row['reservations_count'],
                    (float) $row['avg_actual_time_minutes'],
                    (float) $row['avg_planned_time_minutes'],
                    (float) $row['all_time_minutes']
                ), $data)
            ));
        }

        return $this->stats;
    }


    /** {@inheritDoc} */
    public function getAllUsersStats(): Stats
    {
        $sql = "SELECT u.id, 
                        u.email, 
                        COUNT(res.id) as reservations_count,
                        {$this->avgTimeFileds}
                    FROM {$this->reservations} res 
                        INNER JOIN {$this->users} u ON res.user = u.id 
                    WHERE u.deleted = FALSE AND {$this->between} 
                    GROUP BY u.id 
                    ORDER BY reservations_count";

        $data =  $this->execute($sql);

        $this->stats->addStatsItem(new StatsArray(
            'users',
            array_map(fn ($row) => new UserStatItem(
                (int) $row['id'],
                $row['email'],
                (int) $row['reservations_count'],
                (float) $row['avg_actual_time_minutes'],
                (float) $row['avg_planned_time_minutes'],
                (float) $row['all_time_minutes']
            ), $data)
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
                            COUNT(res.id) as reservations_count, 
                            date_part('dow', res.planned_start) AS day, 
                            {$this->avgTimeFileds}
                        FROM {$this->users} u 
                            INNER JOIN {$this->reservations} res ON res.user = u.id 
                        WHERE u.id = :userId AND {$this->between}
                        GROUP BY day
                        ORDER BY day";

            $data =  $this->execute($sql);

            $this->stats->addStatsItem(new StatsArray(
                'weekly',
                array_map(fn ($row) => new PerDayStatItem(
                    (int) $row['day'],
                    (int) $row['reservations_count'],
                    (float) $row['avg_actual_time_minutes'],
                    (float) $row['avg_planned_time_minutes'],
                    (float) $row['all_time_minutes']
                ), $this->fillDaysOfWeek($data))
            ));
        }
        // Reservations of specific room groupped by day of month
        {
            $sql = "SELECT 
                            COUNT(res.id) as reservations_count,
                            date_part('day', res.planned_start) AS day,
                            {$this->avgTimeFileds}
                        FROM {$this->users} u 
                            INNER JOIN {$this->reservations} res ON res.user = u.id 
                        WHERE u.id = :userId AND {$this->between}
                        GROUP BY day
                        ORDER BY day";

            $data =  $this->execute($sql);

            $this->stats->addStatsItem(new StatsArray(
                'monthly',
                array_map(fn ($row) => new PerDayStatItem(
                    (int) $row['day'],
                    (int)$row['reservations_count'],
                    (float)$row['avg_actual_time_minutes'],
                    (float)$row['avg_planned_time_minutes'],
                    (float)$row['all_time_minutes']
                ), $this->fillDaysOfMonth($data))
            ));
        }
        // Rooms reserved by specific User
        {
            $sql = "SELECT 
                            COUNT(res.id) as reservations_count, 
                            r.name as room_name,
                            b.name as building_name,
                            {$this->avgTimeFileds}
                        FROM {$this->users} u 
                            INNER JOIN {$this->reservations} res ON res.user = u.id 
                            INNER JOIN {$this->rooms} r  ON res.room = r.id 
                            INNER JOIN {$this->buildings} b ON r.building = b.id
                        WHERE u.id = :userId AND {$this->between}
                        GROUP BY res.room, r.name, b.name
                        ORDER BY res.room";

            $data =  $this->execute($sql);

            $this->stats->addStatsItem(new StatsArray(
                'reservedRooms',
                array_map(fn ($row) => new RoomStatItem(
                    $row['room_name'],
                    $row['building_name'],
                    (int)$row['reservations_count'],
                    (float)$row['avg_actual_time_minutes'],
                    (float)$row['avg_planned_time_minutes'],
                    (float)$row['all_time_minutes']
                ), $data)
            ));
        }

        return $this->stats;
    }

    /** {@inheritDoc} */
    public function getAllBuildingsStats(): Stats
    {
        $sql = "SELECT b.id, 
                        b.name,
                        COUNT(res.id) as reservations_count,
                        {$this->avgTimeFileds}
                    FROM {$this->reservations} res 
                        INNER JOIN {$this->rooms} r ON r.id = res.room
                        INNER JOIN {$this->buildings} b ON r.building = b.id
                    WHERE {$this->between}
                    GROUP BY b.id 
                    ORDER BY b.id";

        $data =  $this->execute($sql);

        $this->stats->addStatsItem(new StatsArray(
            'allReservations',
            array_map(fn ($row) => new RoomsOrBuildingReservationsStatItem(
                (int) $row['id'],
                $row['name'],
                (int)$row['reservations_count'],
                (float)$row['avg_actual_time_minutes'],
                (float)$row['avg_planned_time_minutes'],
                (float)$row['all_time_minutes']
            ), $data)
        ));

        return $this->stats;
    }


    /** {@inheritDoc} */
    public function getBuildingStats(int $id): Stats
    {
        $this->params[':buildingId'] = $id;
        // Reservations for specific room groupped by day of week
        {
            $sql = "SELECT COUNT(res.id) as reservations_count,
                            date_part('dow', res.planned_start) AS day,
                            {$this->avgTimeFileds}
                        FROM {$this->reservations} res 
                            INNER JOIN {$this->rooms} r ON r.id = res.room
                            INNER JOIN {$this->buildings} b ON r.building = b.id
                        WHERE b.id = :buildingId AND {$this->between}
                        GROUP BY day";

            $data = $this->execute($sql);

            $this->stats->addStatsItem(new StatsArray(
                'weekly',
                array_map(fn ($row) => new PerDayStatItem(
                    (int) $row['day'],
                    (int)$row['reservations_count'],
                    (float)$row['avg_actual_time_minutes'],
                    (float)$row['avg_planned_time_minutes'],
                    (float)$row['all_time_minutes']
                ), $this->fillDaysOfWeek($data))
            ));
        }
        // Reservations for specific room groupped by day of month
        {
            $sql = "SELECT COUNT(res.id) as reservations_count,
                            date_part('day', res.planned_start) AS day,
                            {$this->avgTimeFileds}
                        FROM {$this->reservations} res 
                            INNER JOIN {$this->rooms} r ON r.id = res.room
                            INNER JOIN {$this->buildings} b ON r.building = b.id
                        WHERE b.id = :buildingId AND {$this->between}
                        GROUP BY day";
            $data =  $this->execute($sql);

            $this->stats->addStatsItem(new StatsArray(
                'monthly',
                array_map(fn ($row) => new PerDayStatItem(
                    (int) $row['day'],
                    (int) $row['reservations_count'],
                    (float) $row['avg_actual_time_minutes'],
                    (float) $row['avg_planned_time_minutes'],
                    (float) $row['all_time_minutes']
                ), $this->fillDaysOfMonth($data))
            ));
        }
        // Users who makes reservation for specific building
        {
            $sql = "SELECT u.id, 
                            u.email, 
                            COUNT(res.id) as reservations_count, 
                            {$this->avgTimeFileds}
                        FROM {$this->reservations} res 
                            INNER JOIN {$this->rooms} r ON r.id = res.room
                            INNER JOIN {$this->buildings} b ON r.building = b.id
                            INNER JOIN {$this->users} u ON res.user = u.id
                        WHERE b.id = :buildingId AND {$this->between}
                        GROUP BY  u.email, u.id 
                        ORDER BY reservations_count";

            $data =  $this->execute($sql);

            $this->stats->addStatsItem(new StatsArray(
                'users',
                array_map(fn ($row) => new UserStatItem(
                    (int) $row['id'],
                    $row['email'],
                    (int)$row['reservations_count'],
                    (float) $row['avg_actual_time_minutes'],
                    (float) $row['avg_planned_time_minutes'],
                    (float) $row['all_time_minutes']
                ), $data)
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
                    REGEXP_REPLACE(endpoint, '/\d+', '/id', 'g') as general_endpoint , 
                    AVG(time) as avg_time,
                    SUM(time) as time_for_endpoint
                FROM {$this->requests} 
                GROUP BY general_endpoint, method 
                ORDER BY general_endpoint ASC;
        ";
        $this->params = [];

        $data =  $this->execute($sql);

        $this->stats->addStatsItem(new StatsArray(
            'endpoints',
            array_map(fn ($row) => new LogStatItem(
                $row['method'],
                (int) $row['calls'],
                $row['general_endpoint'],
                (float) $row['avg_time'],
                (float) $row['time_for_endpoint']
            ), $data)
        ));

        return $this->stats;
    }
}
