<?php

declare(strict_types=1);

namespace App\Infrastructure\Database;

use App\Infrastructure\Database\DatabaseConnectionError;


interface IDatabase
{
    /**
     * connects instance to database
     * @throws DatabaseConnectionError
     */
    public function connect(): void;

    /**
     * implements logic to query database
     */
    public function query(string $queryString, array $params = []): array;

    /**
     * returns id of last inserted row
     */
    public function lastInsertId(): int;
}
