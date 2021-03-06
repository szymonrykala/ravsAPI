<?php

declare(strict_types=1);

namespace App\Infrastructure\Database;

use App\Application\Settings\SettingsInterface;
use PDO;
use PDOException;

use App\Infrastructure\Database\Query;


class MySQL implements IDatabase
{
    /** PDO database connection */
    private PDO $conn;


    public function __construct(
        private SettingsInterface $settings
    ) {
    }

    /**
     * {@inheritDoc}
     */
    public function connect(): void
    {
        // parsing connection string
        $db = parse_url($this->settings->get('databaseUrl'));

        try {
            $this->conn = new PDO(
                "mysql:" . sprintf(
                    "host=%s;port=%s;user=%s;password=%s;dbname=%s;sslmode=require",
                    $db["host"],
                    $db["port"] ?? 3306,
                    $db["user"],
                    $db["pass"],
                    ltrim($db["path"], "/")
                ),
                $db['user'],
                $db["pass"]
            );
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->conn->setAttribute(PDO::ATTR_CASE, PDO::CASE_NATURAL);
        } catch (PDOException $e) {
            throw new DatabaseConnectionError($e->getMessage());
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
