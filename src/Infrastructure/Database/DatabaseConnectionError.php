<?php

declare(strict_types=1);

namespace App\Infrastructure\Database;

use App\Domain\Exception\DomainException;



class DatabaseConnectionError extends DomainException
{
    function __construct()
    {
        $this->message = "Nie udało sie połączyć z bazą danych.";
    }
}
