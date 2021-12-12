<?php
declare(strict_types=1);

namespace App\Domain\User\Exceptions;

use App\Domain\Exception\DomainUnauthenticatedException;


class InvalidUserCodeException extends DomainUnauthenticatedException
{
    public $message = "Podany kod jest nieprawidłowy.";
}