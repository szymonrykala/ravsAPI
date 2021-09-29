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
        $roomId = (int) $this->resolveArg('room_id');
        $buildingId = (int) $this->resolveArg('building_id');
        $addressId = (int) $this->resolveArg('address_id');

        $form = $this->getFormData();

        /** @var Room */
        $room = $this->roomRepository->withBuilding()->byId($roomId);

        if($buildingId !== $room->building->id || $room->building->address !== $addressId)
            throw new HttpBadRequestException(
                $this->request, 
                "Provided data (room id and|or address id) are incorrect."
            );

        $room->rfid = $form->NFCTag;

        $this->roomRepository->save($room);

        return $this->respondWithData();
    }
}
