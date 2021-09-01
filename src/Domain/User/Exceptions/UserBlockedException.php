<?php
declare(strict_types=1);

namespace App\Domain\User\Exceptions;

use App\Domain\Exception\DomainForbiddenOperationException;


class UserBlockedException extends DomainForbiddenOperationException
{
    public $message = "User is blocked - You have to change password to unblock account.";
}
