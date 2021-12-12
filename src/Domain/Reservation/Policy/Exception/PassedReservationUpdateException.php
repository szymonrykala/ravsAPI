<?php

declare(strict_types=1);

namespace App\Domain\Reservation\Policy\Exception;

use App\Domain\Exception\DomainConflictException;



class PassedReservationUpdateException extends DomainConflictException
{
    public $message = 'Rezerwacja już się zakończyła. Nie można jej zaktualizować.';
}
