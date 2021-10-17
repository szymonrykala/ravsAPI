<?php
declare(strict_types=1);

namespace App\Domain\User\Exceptions;

use App\Domain\Exception\DomainForbiddenOperationException;


class DefaultUserDeleteException extends DomainForbiddenOperationException
{
    public $message = "Nie można usunąć użytkownika z predefiniowaną klasą dostępu.";
}
