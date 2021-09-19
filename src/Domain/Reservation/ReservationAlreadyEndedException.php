<?php
declare(strict_types=1);

namespace App\Domain\Reservation;

use App\Domain\Exception\DomainConflictException;

class ReservationAlreadyEndedException extends DomainConflictException
{
    public $message = 'Reservation has already ended. You can not give back the key.';
}
