<?php

declare(strict_types=1);

namespace App\Domain\Reservation\Policy;

use App\Domain\Exception\DomainConflictException;



class PassedReservationUpdateException extends DomainConflictException
{
    public $message = 'Reservation has already passed.';
}