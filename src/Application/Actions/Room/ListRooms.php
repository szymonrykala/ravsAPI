<?php

declare(strict_types=1);

namespace App\Application\Actions\Room;

use Psr\Http\Message\ResponseInterface as Response;


class ListRooms extends RoomAction
{
    /**
     * {@inheritDoc}
     */
    protected function action(): Response
    {
        $buildingId = $this->resolveArg($this::BUILDING_ID, FALSE);

        $rooms = $this->roomRepository
            ->where(['building' => $buildingId])
            ->all();

        $this->logger->info("Rooms list for building id {$buildingId} was viewed.");

        return $this->respondWithData($rooms);
    }
}
