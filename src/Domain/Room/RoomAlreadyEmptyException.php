<?php

declare(strict_types=1);

namespace App\Domain\Room;

use App\Domain\Exception\DomainConflictException;



class RoomAlreadyEmptyException extends DomainConflictException
{
    public $message = 'Room already empty. Do not accept the keys.';
}
