<?php

declare(strict_types=1);

namespace App\Application\Actions\Key;

use Psr\Http\Message\ResponseInterface as Response;
use App\Domain\Room\Room;
use Slim\Exception\HttpBadRequestException;


class DeleteKey extends KeyAction
{
    /**
     * {@inheritDoc}
     */
    protected function action(): Response
    {
        $roomId = $this->resolveArg($this::ROOM_ID);
        $buildingId = $this->resolveArg($this::BUILDING_ID, FALSE);

        $params = ['id' => (int)  $roomId];
        $buildingId && $params['building'] = (int) $buildingId;

        /** @var Room $room */
        $room = $this->roomRepository
            ->withBuilding()
            ->where($params)->one();

        if ($buildingId && (int) $buildingId !== $room->building->id)
            throw new HttpBadRequestException(
                $this->request,
                "Dane pokoju są nieprawidłowe."
            );

        $room->rfid = NULL;

        $this->roomRepository->save($room);
        $this->logger->info("RFIDTag was removed for {$room->id}");

        return $this->respondWithData(NULL);
    }
}
