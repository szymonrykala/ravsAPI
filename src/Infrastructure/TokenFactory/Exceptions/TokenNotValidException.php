<?php

declare(strict_types=1);

namespace App\Infrastructure\TokenFactory\Exceptions;

use App\Domain\Exception\DomainBadRequestException;



class TokenNotValidException extends DomainBadRequestException
{
    public $message = "Token jest nieprawidłowy.";
}
