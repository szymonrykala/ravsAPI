<?php

declare(strict_types=1);

namespace App\Infrastructure\Database;

use App\Domain\Exception\DomainBadRequestException;


class DataIntegrityException extends DomainBadRequestException
{
    public function __construct(string $message)
    {
        $this->message = "Błąd bazy danych:" . $this->processMessage($message);
    }

    private function processMessage(string $message): string
    {
        preg_match('/\s\d{4,}(.*)$/', $message, $output_array);

        return array_pop($output_array) ?? $message;
    }
}
