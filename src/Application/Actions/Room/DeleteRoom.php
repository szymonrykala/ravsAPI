<?php

declare(strict_types=1);

namespace App\Application\Actions\Room;

use Psr\Http\Message\ResponseInterface as Response;

use App\Domain\Room\Room;


class DeleteRoom extends RoomAction
{
    /**
     * {@inheritDoc}
     */
    protected function action(): Response
    {
        $params = $this->getUriParams();

        /** @var Room $room */
        $room = $this->roomRepository
            ->where($params)
            ->one();

        $this->roomRepository->delete($room);

        $this->logger->info("Room id {$room->id} was deleted.");

        return $this->respondWithData();
    }
}
