<?php

declare(strict_types=1);

namespace App\Application\Actions\Stats;


use App\Application\Actions\Action;
use App\Domain\Stats\IStatsRepository;
use Psr\Container\ContainerInterface;
use Psr\Log\LoggerInterface;
use stdClass;

abstract class StatsAction extends Action
{
    protected IStatsRepository $statsRepository;

    public function __construct(ContainerInterface $di)
    {
        parent::__construct($di->get(LoggerInterface::class));

        $this->statsRepository = $di->get(IStatsRepository::class);
    }

    /**
     * Collects 'from' and 'to' params from query string
     */
    protected function getTimeSpanParans(
        string $defaultFrom = '2 month ago',
        string $defaultTo = 'now'
    ): stdClass {
        return  (object) [
            'from' => $this->resolveQueryArg('from', $defaultFrom),
            'to' => $this->resolveQueryArg('to', $defaultTo),
        ];
    }
}
