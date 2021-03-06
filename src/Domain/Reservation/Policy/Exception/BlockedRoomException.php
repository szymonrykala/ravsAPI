<?php

declare(strict_types=1);

namespace App\Domain\Reservation\Policy\Exception;

use App\Domain\Exception\DomainBadRequestException;



class BlockedRoomException extends DomainBadRequestException
{
    public function __construct(?string $message = NULL)
    {
        $this->message = $message ?? 'Pokój jest zablokowany. Nie można go zarezerwować.';
    }
}
