<?php

declare(strict_types=1);

namespace App\Domain\Reservation\Policy\Exception;

use App\Domain\Exception\DomainConflictException;



class TooLateReservationUpdateException extends DomainConflictException
{
    public $message = 'You can not update reservation in 24 hours before start.';
}
