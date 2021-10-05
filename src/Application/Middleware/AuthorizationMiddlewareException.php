<?php

declare(strict_types=1);

namespace App\Application\Middleware;

use App\Domain\Exception\DomainForbiddenOperationException;


class AuthorizationMiddlewareException extends DomainForbiddenOperationException
{
    public $message = 'You are unauthorized to perform this action';
}