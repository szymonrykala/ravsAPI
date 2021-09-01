<?php
declare(strict_types=1);

namespace App\Domain\User\Exceptions;
use App\Domain\Exception\DomainForbiddenOperationException;

class DefaultUserUpdateException extends DomainForbiddenOperationException
{
    public $message = "Can not update default admin user.";
}
