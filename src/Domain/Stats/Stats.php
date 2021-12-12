<?php

declare(strict_types=1);

namespace App\Domain\Stats;

use App\Domain\Model\Model;



final class Stats extends Model
{
    private array $data;

    public function __construct(?StatsArray $item = NULL)
    {
        $this->data = [];
        $item && $this->addStatsItem($item);
    }

    public function addStatsItem(?StatsArray $item): void
    {
        $this->data = array_merge($this->data, $item->toArray());
    }


    /** {@inheritDoc} */
    public function jsonSerialize(): array
    {
        return $this->data;
    }
}


class UserStatItem
{
    public function __construct(
        public int $id,
        public string $email,
        public int $reservationsCount,
        public float $avgActualTimeMinutes,
        public float $avgPlannedTimeMinutes,
        public float $allTimeMinutes,
    ) {
    }
}

class RoomStatItem
{
    public function __construct(
        public string $roomName,
        public string $buildingName,
        public int $reservationsCount,
        public float $avgActualTimeMinutes,
        public float $avgPlannedTimeMinutes,
        public float $allTimeMinutes,
    ) {
    }
}

class RoomsOrBuildingReservationsStatItem
{
    public function __construct(
        public int $id,
        public string $name,
        public int $reservationsCount,
        public float $avgActualTimeMinutes,
        public float $avgPlannedTimeMinutes,
        public float $allTimeMinutes,
    ) {
    }
}

class PerDayStatItem
{
    public function __construct(
        public int $day,
        public int $reservationsCount,
        public float $avgActualTimeMinutes,
        public float $avgPlannedTimeMinutes,
        public float $allTimeMinutes,
    ) {
    }
}

class LogStatItem
{
    public function __construct(
        public string $method,
        public int $calls,
        public string $generalEndpoint,
        public float $avgTime,
        public float $timeForEndpoint,
    ) {
    }
}
