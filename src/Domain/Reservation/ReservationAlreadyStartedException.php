<?php
declare(strict_types=1);

namespace App\Domain\Reservation;

use App\Domain\Exception\DomainConflictException;

class ReservationAlreadyStartedException extends DomainConflictException
{
    public $message = 'Rezerwacja już się rozpoczęła. Klucz został już odebrany.';
}
