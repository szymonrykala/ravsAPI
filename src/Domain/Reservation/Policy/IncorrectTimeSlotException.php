<?php

declare(strict_types=1);

namespace App\Domain\Reservation\Policy;

use App\Domain\Exception\DomainConflictException;



class IncorrectTimeSlotException extends DomainConflictException
{
    public function __construct(?string $message = NULL)
    {
        $this->message = $message ?? 'Reservation time slot is not correct.';
    }
}