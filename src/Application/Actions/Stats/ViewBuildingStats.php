<?php

declare(strict_types=1);

namespace App\Application\Actions\Stats;

use Psr\Http\Message\ResponseInterface as Response;

use App\Domain\Stats\Stats;
use App\Utils\JsonDateTime;

class ViewBuildingStats extends StatsAction
{
    /**
     * {@inheritdoc}
     */
    protected function action(): Response
    {
        $buildingId =  $this->resolveArg('building_id', FALSE);

        $from = $this->resolveQueryArg('from', FALSE);
        $to = $this->resolveQueryArg('to', 'now');

        if (!$from) {
            $date = new JsonDateTime('3 month ago');
            $from = $date->getDate();
        }

        $this->statsRepository->setTimeSpan($from, $to);

        /** @var Stats $stats */
        $stats = ($buildingId !== FALSE) ?
            $this->statsRepository->getBuildingStats((int)$buildingId)
            : $this->statsRepository->getAllBuildingsStats();

        $this->logger->info("Stats for buildings {$buildingId} was viewed");

        return $this->respondWithData($stats);
    }
}
