<?php

declare(strict_types=1);

namespace App\Application\Actions\Stats;

use Psr\Http\Message\ResponseInterface as Response;

use App\Domain\Stats\Stats;



final class ViewUserStats extends StatsAction
{
    /**
     * {@inheritDoc}
     */
    protected function action(): Response
    {
        $userId =  $this->resolveArg($this::USER_ID, FALSE);
        $time = $this->getTimeSpanParans();

        $this->statsRepository->setTimeSpan($time->from, $time->to);

        /** @var Stats $stats */
        $stats = ($userId !== FALSE) ?
            $this->statsRepository->getUserStats((int)$userId)
            : $this->statsRepository->getAllUsersStats();

        $this->logger->info("Stats for rooms {$userId} was viewed");

        return $this->respondWithData($stats);
    }
}
