<?php

declare(strict_types=1);

namespace App\Domain\Reservation;

use App\Domain\Exception\DomainConflictException;


class StartedReservationDeleteException extends DomainConflictException
{
    public $message = 'Nie mozna usunąć trwającej rezerwacji.';
}
