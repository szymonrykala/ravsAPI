<?php

declare(strict_types=1);

namespace App\Domain\Exception;

class DomainUnauthorizedOperationException extends DomainException
{
    public $message = "You have unsufficient privilages to perform this operation.";
}
