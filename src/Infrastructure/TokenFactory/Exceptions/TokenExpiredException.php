<?php

declare(strict_types=1);

namespace App\Infrastructure\TokenFactory\Exceptions;

use App\Domain\Exception\DomainUnauthenticatedException;


class TokenExpiredException extends DomainUnauthenticatedException
{
    public $message = "Token wygasł. Zaloguj się ponownie.";
}
