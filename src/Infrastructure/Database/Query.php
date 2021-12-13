<?php

declare(strict_types=1);

namespace App\Infrastructure\Database;

use PDO;
use PDOException;


class Query
{
    /**
     * @param PDO $database connection
     * @param string $sql
     * @param array $sql params
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
            // var_dump($e->getMessage());
            // var_dump($e->getCode());
            $message = '';
            switch ($e->getCode()) {
                case '23505':
                    preg_match('/DETAIL:.*=\((.*)\)/', $e->getMessage(), $outputArray);
                    $message = isset($outputArray[1]) ? "'$outputArray[1]'" : 'podana wartość';

                    $error->message = "Konflikt, $message już istnieje.";
                    break;
                case '23503':
                    preg_match('/on\stable\s\"(.*)\"\sviolates/', $e->getMessage(), $outputArray);

                    $tables = [
                        'building' => 'Budynek',
                        'room' => 'Sala',
                        'user' => 'Użytkownik',
                        'access' => 'Klasa dostępu'
                    ];
                    if (isset($outputArray[1], $tables[$outputArray[1]]))
                        $error->message = 'Nie można usunąć. '
                            . $tables[$outputArray[1]]
                            . ' zawiera referencyjne obiekty.';
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
