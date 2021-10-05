<?php
declare(strict_types=1);

namespace App\Domain\Reservation;

use App\Domain\Exception\DomainConflictException;

class ReservationAlreadyStartedException extends DomainConflictException
{
    public $message = 'Reservation has already started. You can not pick up the key.';
}
