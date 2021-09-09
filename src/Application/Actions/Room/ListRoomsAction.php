<?php
declare(strict_types=1);

namespace App\Application\Actions\Room;

use Psr\Http\Message\ResponseInterface as Response;

use App\Domain\Image\Image;
use App\Domain\Building\Building;
use App\Domain\Room\Room;

class ListRoomsAction extends RoomAction
{
    /**
     * {@inheritdoc}
     */
    protected function action(): Response
    {
        $buildingId = (int) $this->resolveArg('building_id');
        
        $rooms = $this->roomRepository
                    ->where(['building' => $buildingId])
                    ->all();

        $this->logger->info("Rooms list for building id $buildingId was viewed.");

        return $this->respondWithData($rooms);
    }
}