<?php

declare(strict_types=1);

namespace App\Application\Actions\Room;

use Psr\Http\Message\ResponseInterface as Response;

use App\Domain\Room\Room;


class ViewRoom extends RoomAction
{
    /**
     * {@inheritDoc}
     */
    protected function action(): Response
    {
        $params = $this->getUriParams();

        /** @var Room $room */
        $room = $this->roomRepository
            ->withBuilding()
            ->where($params)->one();


        $this->logger->info("Room id {$room->id} was viewed.");

        return $this->respondWithData($room);
    }
}
