<?php
declare(strict_types=1);

namespace App\Domain\User\Exceptions;

use App\Domain\Exception\DomainBadRequestException;


class BadCredentialsException extends DomainBadRequestException
{
    public $message = "Podane hasło jest nieprawidłowe.";
}
