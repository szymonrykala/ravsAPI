<?php

declare(strict_types=1);

namespace App\Application\Actions\Stats;

use Psr\Http\Message\ResponseInterface as Response;

use App\Domain\Stats\Stats;
use App\Utils\JsonDateTime;

class ViewRequestStats extends StatsAction
{
    /**
     * {@inheritdoc}
     */
    protected function action(): Response
    {
        $from = $this->resolveQueryArg('from', FALSE);
        $to = $this->resolveQueryArg('to', 'now');

        if (!$from) {
            $date = new JsonDateTime('1 month ago');
            $from = $date->getDate();
        }

        $this->statsRepository->setTimeSpan($from, $to);

        /** @var Stats $stats */
        $stats = $this->statsRepository->getRequestsStats();

        $this->logger->info("Requests stats was viewed");

        return $this->respondWithData($stats);
    }
}
