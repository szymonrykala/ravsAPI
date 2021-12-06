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
            $error = new DataIntegrityException('');
            var_dump($e->getMessage());
            // var_dump($e->getCode());
            $message = '';
            switch ($e->getCode()) {
                case '23000':
                    preg_match('/Duplicate\sentry\s\'(.*)\'\s/', $e->getMessage(), $outputArray);
                    $message = isset($outputArray[1]) ? "'$outputArray[1]'" : 'podana wartość';
                    $error->message = "Niestesty $message już istnieje.";
                    break;
                case '42S22':
                    preg_match('/Unknown\scolumn\s\'(.*)\'\s/', $e->getMessage(), $outputArray);
                    $message = isset($outputArray[1]) && $outputArray[1];
                    $error->message = "Właściwość '$message' nie istnieje.";
                case '22007':
                    $error->message = 'Błąd składni SQL: ' . $e->getMessage();
                    break;
                default:
                    $message = $e->getMessage();
                    # code...
                    break;
            }
            throw $error;
        }
        // $r = $this->statement->fetchAll(PDO::FETCH_ASSOC);
        // print_r($r);
        return $this->statement->fetchAll(PDO::FETCH_ASSOC);
    }
}
