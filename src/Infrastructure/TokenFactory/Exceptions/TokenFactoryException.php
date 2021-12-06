<?php

declare(strict_types=1);

namespace App\Infrastructure\TokenFactory\Exceptions;

use DomainException;

class TokenFactoryException extends DomainException
{
    public $message = "Nie udało się wygenerować tokenu. Spróbuj jeszcze raz.";
}
