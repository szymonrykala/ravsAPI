<?php

declare(strict_types=1);

namespace App\Application\Actions\Stats;

use Psr\Http\Message\ResponseInterface as Response;

use App\Domain\Stats\Stats;


final class ViewRoomStats extends StatsAction
{
    /**
     * {@inheritDoc}
     */
    protected function action(): Response
    {
        $roomId =  $this->resolveArg($this::ROOM_ID, FALSE);
        $time = $this->getTimeSpanParans();

        $this->statsRepository->setTimeSpan($time->from, $time->to);

        /** @var Stats $stats */
        $stats = ($roomId !== FALSE) ?
            $this->statsRepository->getRoomStats((int)$roomId)
            : $this->statsRepository->getAllRoomsStats();

        $this->logger->info("Stats for rooms {$roomId} was viewed");

        return $this->respondWithData($stats);
    }
}
