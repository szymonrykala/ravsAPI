<?php

declare(strict_types=1);

namespace App\Infrastructure\Database;

use PDO;
use PDOException;


class Query
{
    /**
     * @param PDO $conn
     * @param string $sql
     * @param array $params=[]
     */
    public function __construct(PDO $conn, string $sql, array $params = [])
    {
        $this->statement = $conn->prepare($sql);
        $this->params = $params;
    }

    /**
     * @return array
     */
    public function execute(): array
    {
        try {
            $this->statement->execute($this->params);
        } catch (PDOException $e) {
            // var_dump($e->getMessage());       
            throw new DataIntegrityException($e->getMessage());
            switch ($e->getCode()) {
                case '':
                    # code...
                    break;

                default:
                    # code...
                    break;
            }
        }

        return $this->statement->fetchAll();
    }
}
