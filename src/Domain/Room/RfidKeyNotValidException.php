<?php

declare(strict_types=1);

namespace App\Domain\Room;

use App\Domain\Exception\DomainConflictException;



class RfidKeyNotValidException extends DomainConflictException
{
    public $message = 'Provided RFID key is not valid for this room.';
}
