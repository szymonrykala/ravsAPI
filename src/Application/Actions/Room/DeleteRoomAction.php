<?php

declare(strict_types=1);

namespace App\Application\Actions\Room;

use Psr\Http\Message\ResponseInterface as Response;

use App\Domain\Room\Room;


class DeleteRoomAction extends RoomAction
{
    /**
     * {@inheritdoc}
     */
    protected function action(): Response
    {
        $roomId = (int) $this->resolveArg('room_id');
        $buildingId = (int) $this->resolveArg('building_id');

        /** @var Room $room */
        $room = $this->roomRepository->where([
            'id' => $roomId,
            'building' => $buildingId
        ])->one();

        $this->roomRepository->delete($room);

        $this->logger->info("Room id $roomId was deleted.");

        return $this->respondWithData();
    }
}
