<?php

declare(strict_types=1);

namespace App\Application\Actions\Key;

use Psr\Http\Message\ResponseInterface as Response;
use App\Domain\Room\Room;
use Slim\Exception\HttpBadRequestException;

class AssignKey extends KeyAction
{
    /**
     * {@inheritDoc}
     */
    protected function action(): Response
    {
        $roomId = (int) $this->resolveArg($this::ROOM_ID);
        $buildingId = (int) $this->resolveArg($this::BUILDING_ID);
        $addressId = (int) $this->resolveArg($this::ADDRESS_ID);

        $form = $this->getFormData();

        /** @var Room */
        $room = $this->roomRepository->withBuilding()->byId($roomId);

        if($buildingId !== $room->building->id || $room->building->addressId !== $addressId)
            throw new HttpBadRequestException(
                $this->request, 
                "Dane pokoju są nieprawidłowe."
            );

        $room->rfid = $form->RFIDTag;

        $this->roomRepository->save($room);

        return $this->respondWithData();
    }
}
