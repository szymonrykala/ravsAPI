<?php

declare(strict_types=1);

namespace App\Domain\Reservation\Policy\Exception;

use App\Domain\Exception\DomainConflictException;


class TimeSlotConflictException extends DomainConflictException
{
    public function __construct(?string $message = NULL)
    {
        $this->message = $message ?? 'Zarezerwowany czas jest już zajęty przez kogoś innego.';
    }
}
