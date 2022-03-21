<?php

declare(strict_types=1);

namespace App\Application\Actions\Stats;

use Psr\Http\Message\ResponseInterface as Response;

use App\Domain\Stats\Stats;
use App\Utils\JsonDateTime;


final class ViewBuildingStats extends StatsAction
{
    /**
     * {@inheritDoc}
     */
    protected function action(): Response
    {
        $buildingId =  $this->resolveArg($this::BUILDING_ID, FALSE);
        $addressId = $this->resolveArg($this::ADDRESS_ID);

        $date = new JsonDateTime('3 month ago');
        $time=$this->getTimeSpanParans($date->getDate());

        $this->statsRepository->setTimeSpan($time->from, $time->to);

        /** @var Stats $stats */
        $stats = ($buildingId !== FALSE) ?
            $this->statsRepository->getBuildingStats((int)$buildingId)
            : $this->statsRepository->getAllBuildingsStats((int)$addressId);

        $this->logger->info("Stats for buildings {$buildingId} was viewed");

        return $this->respondWithData($stats);
    }
}
