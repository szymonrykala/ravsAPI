<?php

declare(strict_types=1);

namespace App\Application\Actions\Stats;


use App\Application\Actions\Action;
use App\Domain\Stats\IStatsRepository;
use Psr\Log\LoggerInterface;


abstract class StatsAction extends Action
{
    protected IStatsRepository $statsRepository;

    public function __construct(
        LoggerInterface $logger,
        IStatsRepository $statsRepository
    ) {
        parent::__construct($logger);

        $this->statsRepository = $statsRepository;
    }
}
