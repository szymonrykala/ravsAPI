<?php

declare(strict_types=1);

namespace App\Application\Actions\Room;

use Psr\Http\Message\ResponseInterface as Response;
use App\Domain\Room\Room;
use App\Domain\Room\Validation\UpdateValidator;
use stdClass;


class UpdateRoom extends RoomAction
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

        /** @var stdClass $form */
        $form = $this->getFormData();

        $validator = new UpdateValidator();
        $validator->validateForm($form);

        $room->update($form);

        $this->roomRepository->save($room);

        $this->logger->info("Room id {$room->id} was updated.");

        return $this->respondWithData();
    }
}
