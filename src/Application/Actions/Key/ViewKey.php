<?php

declare(strict_types=1);

namespace App\Application\Actions\Key;

use Psr\Http\Message\ResponseInterface as Response;
use App\Domain\Room\Room;


class ViewKey extends KeyAction
{
    /**
     * {@inheritDoc}
     */
    protected function action(): Response
    {
        $roomId = $this->resolveArg('room_id');
        $buildingId = $this->resolveArg('building_id', FALSE);

        $params = ['id' => (int)  $roomId];
        $buildingId && $params['building'] = (int) $buildingId;

        /** @var Room $room */
        $room = $this->roomRepository
            ->withBuilding()
            ->where($params)->one();


        $this->logger->info("NFC key of room id {$room->id} was viewed");

        return $this->respondWithData($room->rfid);
    }
}
