<?php

declare(strict_types=1);

namespace App\Application\Actions\Stats;

use Psr\Http\Message\ResponseInterface as Response;

use App\Domain\Stats\Stats;
use App\Utils\JsonDateTime;

class ViewRoomStats extends StatsAction
{
    /**
     * {@inheritdoc}
     */
    protected function action(): Response
    {
        $roomId =  $this->resolveArg('room_id', FALSE);

        $from = $this->resolveQueryArg('from', FALSE);
        $to = $this->resolveQueryArg('to', 'now');

        if (!$from) {
            $date = new JsonDateTime('1 month ago');
            $from = $date->getDate();
        }

        $this->statsRepository->setTimeSpan($from, $to);

        /** @var Stats $stats */
        $stats = ($roomId !== FALSE) ?
            $this->statsRepository->getRoomStats((int)$roomId)
            : $this->statsRepository->getAllRoomsStats();

        $this->logger->info("Stats for rooms {$roomId} was viewed");

        return $this->respondWithData($stats);
    }
}
