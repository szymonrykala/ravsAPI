<?php

declare(strict_types=1);

namespace App\Domain\Reservation\Policy;

use App\Domain\Exception\DomainBadRequestException;



class BlockedRoomException extends DomainBadRequestException
{
    public function __construct(?string $message = NULL)
    {
        $this->message = $message ?? 'Room is blocked, You can not reserve it';
    }
}
