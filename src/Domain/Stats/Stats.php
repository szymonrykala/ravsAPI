<?php

declare(strict_types=1);

namespace App\Domain\Stats;

use App\Domain\Model\Model;



final class Stats extends Model
{
    private array $data;

    public function __construct(?StatsItem $item = NULL)
    {
        $this->data = [];
        $item && $this->addStatsItem($item);
    }

    public function addStatsItem(?StatsItem $item): void
    {
        $this->data = array_merge($this->data, $item->toArray());
    }


    /** {@inheritDoc} */
    public function jsonSerialize(): array
    {
        return $this->data;
    }
}
