<?php

declare(strict_types=1);

namespace App\Domain\Room;

use App\Domain\Exception\DomainConflictException;



class RfidKeyNotValidException extends DomainConflictException
{
    public $message = 'Podany klucz nie pasuje do tego pokoju.';
}
