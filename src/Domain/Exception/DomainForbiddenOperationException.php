<?php

declare(strict_types=1);

namespace App\Domain\Exception;

class DomainForbiddenOperationException extends DomainException
{
    public $message = "Operation You want to perform is not permitted.";
}
