<?php

declare(strict_types=1);

namespace App\Domain\Exception;

class DomainUnauthorizedOperationException extends DomainException
{
    public $message = "Nie masz wystarczających uprawnień by wykonać tą operację.";
}
