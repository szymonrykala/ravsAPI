<?php
declare(strict_types=1);

namespace App\Domain\User\Exceptions;

use App\Domain\Exception\DomainBadRequestException;


class InvalidUserCodeException extends DomainBadRequestException
{
    public $message = "Provided code is not correct.";
}
