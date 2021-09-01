<?php
declare(strict_types=1);

namespace App\Domain\User;

use App\Domain\Exception\DomainBadRequestException;

class IncorrectPasswordException extends DomainBadRequestException
{
    public $message = "Provided password is not corresponding to regulations.";
}
