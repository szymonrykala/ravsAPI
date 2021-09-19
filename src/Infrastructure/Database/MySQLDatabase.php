<?php

declare(strict_types=1);

namespace App\Infrastructure\Database;

use PDO;
use PDOException;

use App\Infrastructure\Database\Query;


class MySQLDatabase implements IDatabase
{

    private PDO $conn;

    /**
     * @param string $user
     * @param string $password
     * @param string $host
     * @param string $name
     */
    public function __construct(
        string $user,
        string $password,
        string $host,
        string $name
    ) {
        $this->user = $user;
        $this->password = $password;
        $this->host = $host;
        $this->name = $name;
    }

    /**
     * {@inheritDoc}
     */
    public function connect(): void
    {
        try {
            $this->conn = new PDO(
                'mysql:host=' . $this->host . ';dbname=' . $this->name . ';charset=utf8mb4',
                $this->user,
                $this->password
            );
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            throw new DatabaseConnectionError();
        }
    }

    /**
     * {@inheritDoc}
     */
    public function query(string $sql, array $params = []): array
    {
        $query = new Query($this->conn, $sql, $params);
        return $query->execute();
    }

    /**
     * {@inheritDoc}
     */
    public function lastInsertId(): int
    {
        return (int) $this->conn->lastInsertId();
    }
}
