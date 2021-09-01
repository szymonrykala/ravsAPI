<?php
declare(strict_types=1);

namespace App\Application\Actions\Room;

use Psr\Http\Message\ResponseInterface as Response;

use App\Domain\Room\Room;
use Slim\Exception\HttpBadRequestException;

use stdClass;

class UpdateRoomAction extends RoomAction
{
    /**
     * {@inheritdoc}
     */
    protected function action(): Response
    {
        $roomId = (int) $this->resolveArg('room_id');
        $buildingId = (int) $this->resolveArg('building_id');

        /** @var Room $room */
        $room = $this->roomRepository->byIdAndBuildingId($roomId, $buildingId);

        /** @var stdClass $form */
        $form = $this->getFormData();

        $room->update($form);



        $this->roomRepository->save($room);

        $this->logger->info("Room id $roomId was updated.");

        return $this->respondWithData();
    }
}