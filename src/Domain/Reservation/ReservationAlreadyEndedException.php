<?php
declare(strict_types=1);

namespace App\Domain\Reservation;

use App\Domain\Exception\DomainConflictException;

class ReservationAlreadyEndedException extends DomainConflictException
{
    public $message = 'Rezerwacja już się zakończyła. Klucz został już oddany.';
}
