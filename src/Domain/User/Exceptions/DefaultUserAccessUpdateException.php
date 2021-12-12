<?php
declare(strict_types=1);

namespace App\Domain\User\Exceptions;

use App\Domain\Exception\DomainForbiddenOperationException;


class DefaultUserAccessUpdateException extends DomainForbiddenOperationException
{
    public $message = "Nie możesz zmieniać dostępu predefiniowanego użytkownika.";
}
