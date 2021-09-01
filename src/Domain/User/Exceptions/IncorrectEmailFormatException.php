<?php
declare(strict_types=1);

namespace App\Domain\User;

use App\Domain\Exception\DomainBadRequestException;

class IncorrectEmailFormatException extends DomainBadRequestException
{
    public $message = "Provided email is not in correct format.";
}
