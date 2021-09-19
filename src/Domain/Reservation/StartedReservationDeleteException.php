<?php

declare(strict_types=1);

namespace App\Domain\Reservation;

use App\Domain\Exception\DomainConflictException;


class StartedReservationDeleteException extends DomainConflictException
{
    public $message = 'Reservation is or was active in past. You can not delete it.';
}
