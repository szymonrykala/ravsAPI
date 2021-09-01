<?php

declare(strict_types=1);

namespace App\Domain\Exception;

class DomainConflictException extends DomainException
{
    public $message = "Domain has encountered a conflict.";
}
