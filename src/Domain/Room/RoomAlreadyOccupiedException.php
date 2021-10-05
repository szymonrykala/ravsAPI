<?php

declare(strict_types=1);

namespace App\Domain\Room;

use App\Domain\Exception\DomainConflictException;



class RoomAlreadyOccupiedException extends DomainConflictException
{
    public $message = 'Room already occupied. Do not issue the keys.';
}
