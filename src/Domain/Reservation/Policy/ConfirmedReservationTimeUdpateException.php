<?php

declare(strict_types=1);

namespace App\Domain\Reservation\Policy;

use App\Domain\Exception\DomainConflictException;


class ConfirmedReservationTimeUdpateException extends DomainConflictException
{
    public function __construct()
    {
        $this->message = "Cannot update time slot of the confirmed reservation.";
    }
}
