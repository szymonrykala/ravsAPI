<?php
declare(strict_types=1);

namespace App\Domain\User\Exceptions;

use App\Domain\Exception\DomainBadRequestException;


class DefaultUserDeleteException extends DomainBadRequestException
{
    public $message = "Nie można usunąć użytkownika z predefiniowaną klasą dostępu.";
}
