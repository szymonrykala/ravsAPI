<?php

declare(strict_types=1);

namespace App\Infrastructure\Database;

use PDO;
use PDOException;


class Query
{

    public function __construct(PDO $conn, string $sql, array $params = [])
    {
        $this->statement = $conn->prepare($sql);
        $this->params = $params;
    }

    /**
     * Executes sql query and handle errors
     */
    public function execute(): array
    {
        try {
            $this->statement->execute($this->params);
        } catch (PDOException $e) {
            $error = new DataIntegrityException('');

            $message = '';
            switch ($e->getCode()) {
                case '23000':
                    $message = "Nie można obecnie wykonać tej operacji."; {
                        preg_match('/Duplicate\sentry\s\'(.*)\'\sfor\s/', $e->getMessage(), $outputArray);
                        if (isset($outputArray[1])) {
                            $message = "Spróbuj innych danych, '$outputArray[1]' już istnieje.";
                        }
                    } {
                        preg_match('/FOREIGN\sKEY\s\(\`(.*)`\)\sREFERENCES/', $e->getMessage(), $outputArray);

                        $tables = [
                            'building' => 'Budynek',
                            'room' => 'Sala',
                            'user' => 'Użytkownik',
                            'access' => 'Klasa dostępu',
                            'address' => 'Adres'
                        ];

                        if (isset($outputArray[1], $tables[$outputArray[1]])) {
                            $message = 'Nie można usunąć. ' . $tables[$outputArray[1]] . ' zawiera referencyjne obiekty.';
                        }
                    }
                    $error->message = $message;
                    break;
                default:
                    $error->message = $e->getMessage();
                    # code...
                    break;
            }
            throw $error;
        }

        return $this->statement->fetchAll(PDO::FETCH_ASSOC);
    }
}
