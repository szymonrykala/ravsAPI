<?php

declare(strict_types=1);

namespace App\Infrastructure\Database;

use App\Domain\Exception\DomainConflictException;


class DataIntegrityException extends DomainConflictException
{
    public function __construct(string $message)
    {
        $this->message = "Niestety '$message' już istnieje.";
    }
}
