<?php

declare(strict_types=1);

namespace App\Infrastructure\Database;

use App\Infrastructure\Database\DatabaseConnectionError;
use PDO;

interface IDatabase
{
    /**
     * connects instance to database
     * @return void
     * @throws DatabaseConnectionError
     */
    public function connect(): void;

    /**
     * implements logic to query database
     * @param string $queryString
     * @param array $params
     * @return array
     */
    public function query(string $queryString, array $params): array;

    /**
     * @param int $id
     */
    public function lastInsertId(): int;
}
