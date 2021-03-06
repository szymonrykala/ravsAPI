<?php
declare(strict_types=1);

namespace App\Domain\User\Exceptions;

use App\Domain\Exception\DomainForbiddenOperationException;


class UserNotActivatedException extends DomainForbiddenOperationException
{
    public $message = "Użytkownik nie jest aktywny.";
}
