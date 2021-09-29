<?php
declare(strict_types=1);

namespace App\Application\Actions\Room;

use Psr\Http\Message\ResponseInterface as Response;

use App\Domain\Room\Room;


class CreateRoom extends RoomAction
{
    /**
     * {@inheritdoc}
     */
    protected function action(): Response
    {
        $buildingId = (int) $this->resolveArg('building_id');
        $addressId = (int) $this->resolveArg('address_id');

        $this->buildingRepository->byIdAndAddressId($buildingId, $addressId);

        $form = $this->getformData();

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