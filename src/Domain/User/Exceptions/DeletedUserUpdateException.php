<?php
declare(strict_types=1);

namespace App\Domain\User\Exceptions;
use App\Domain\Exception\DomainForbiddenOperationException;


class DeletedUserUpdateException extends DomainForbiddenOperationException
{
    public $message = "Nie można aktualizować usuniętego użytkownika.";
}
