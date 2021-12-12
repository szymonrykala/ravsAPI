<?php
declare(strict_types=1);

namespace App\Domain\User\Exceptions;

use App\Domain\Exception\DomainForbiddenOperationException;


class UserBlockedException extends DomainForbiddenOperationException
{
    public $message = "Użytkownik jest zablokowany. Zmień hasło aby odblokować konto.";
}
