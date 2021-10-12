<?php

declare(strict_types=1);

namespace App\Domain\Reservation\Policy\Exception;

use App\Domain\Exception\DomainConflictException;


class RoomBuildingConflictException extends DomainConflictException
{
    public function __construct(int $roomId, int $buildingId)
    {
        $this->message = "Pokój (id='${roomId}') nie znajduje się w budynku (id='${buildingId}').";
    }
}
