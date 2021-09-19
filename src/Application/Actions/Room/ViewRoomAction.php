<?php

declare(strict_types=1);

namespace App\Application\Actions\Room;

use Psr\Http\Message\ResponseInterface as Response;

use App\Domain\Image\Image;
use App\Domain\Building\Building;
use App\Domain\Room\Room;


class ViewRoomAction extends RoomAction
{
    /**
     * {@inheritdoc}
     */
    protected function action(): Response
    {
        $roomId = (int) $this->resolveArg('room_id');
        $buildingId = (int) $this->resolveArg('building_id');

        /** @var Room $room */
        $room = $this->roomRepository
            ->withBuilding()
            ->where([
                'id' => $roomId,
                'building' => $buildingId
            ])->one();
        // ->byIdAndBuildingId($roomId, $buildingId);

        $this->logger->info("Room id $roomId was viewed.");

        return $this->respondWithData($room);
    }
}
