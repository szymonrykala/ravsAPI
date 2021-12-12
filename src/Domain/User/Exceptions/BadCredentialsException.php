<?php
declare(strict_types=1);

namespace App\Domain\User\Exceptions;

use App\Domain\Exception\DomainUnauthenticatedException;


class BadCredentialsException extends DomainUnauthenticatedException
{
    public $message = "Podane hasło jest nieprawidłowe.";
}
