<?php
declare(strict_types=1);

namespace App\Application\Actions\Room;

use Psr\Http\Message\ResponseInterface as Response;



class CreateRoom extends RoomAction
{
    /**
     * {@inheritDoc}
     */
    protected function action(): Response
    {
        $buildingId = (int) $this->resolveArg($this::BUILDING_ID);
        $addressId = (int) $this->resolveArg($this::ADDRESS_ID);
        
        $form = $this->getformData();
        
        $this->buildingRepository->byIdAndAddressId($buildingId, $addressId);

        $newRoomId = $this->roomRepository->create(
            $form->name,
            $buildingId,
            $form->roomType,
            $form->seatsCount,
            $form->floor
        );
        return $this->respondWithData($newRoomId);
    }
}