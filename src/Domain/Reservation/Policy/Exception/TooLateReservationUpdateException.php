<?php

declare(strict_types=1);

namespace App\Domain\Reservation\Policy\Exception;

use App\Domain\Exception\DomainConflictException;



class TooLateReservationUpdateException extends DomainConflictException
{
    public $message = 'Nie można aktualizować rezerwacji 24 godziny przed planowanym rozpoczęciem.';
}
