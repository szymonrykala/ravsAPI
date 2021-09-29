<?php

declare(strict_types=1);

namespace App\Domain\Stats;

use JsonSerializable;


class StatsItem
{
    /** Title of data table */
    private string $title;

    /** Table of data */
    private array $data;

    public function __construct(string $title,  array $chartData)
    {
        $this->title = $title;
        $this->data = $chartData;
    }

    /** {@inheritDoc} */
    public function toArray(): array
    {
        return [$this->title => $this->data];
    }
}