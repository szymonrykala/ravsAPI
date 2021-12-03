<?php

declare(strict_types=1);

namespace App\Application\Actions\Stats;

use Psr\Http\Message\ResponseInterface as Response;

use App\Domain\Stats\Stats;


final class ViewRequestStats extends StatsAction
{
    /**
     * {@inheritDoc}
     */
    protected function action(): Response
    {
        /** @var Stats $stats */
        $stats = $this->statsRepository->getRequestsStats();

        $this->logger->info("Requests stats was viewed");

        return $this->respondWithData($stats);
    }
}
