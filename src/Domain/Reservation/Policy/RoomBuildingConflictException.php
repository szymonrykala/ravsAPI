<?php

declare(strict_types=1);

namespace App\Domain\Reservation\Policy;

use App\Domain\Exception\DomainConflictException;


class RoomBuildingConflictException extends DomainConflictException
{
    public function __construct(int $roomId, int $buildingId)
    {
        $this->message = "Given room (id='${roomId}') does not contain in building (id='${buildingId}').";
    }
}
