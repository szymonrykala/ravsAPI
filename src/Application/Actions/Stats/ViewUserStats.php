<?php

declare(strict_types=1);

namespace App\Application\Actions\Stats;

use Psr\Http\Message\ResponseInterface as Response;

use App\Domain\Stats\Stats;
use App\Utils\JsonDateTime;



class ViewUserStats extends StatsAction
{
    /**
     * {@inheritdoc}
     */
    protected function action(): Response
    {
        $userId =  $this->resolveArg('user_id', FALSE);

        $from = $this->resolveQueryArg('from', FALSE);
        $to = $this->resolveQueryArg('to', 'now');

        if (!$from) {
            $date = new JsonDateTime('1 month ago');
            $from = $date->getDate();
        }

        $this->statsRepository->setTimeSpan($from, $to);

        /** @var Stats $stats */
        $stats = ($userId !== FALSE) ?
            $this->statsRepository->getUserStats((int)$userId)
            : $this->statsRepository->getAllUsersStats();

        $this->logger->info("Stats for rooms {$userId} was viewed");

        return $this->respondWithData($stats);
    }
}
