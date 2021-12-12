<?php

declare(strict_types=1);

namespace App\Domain\Exception;

class DomainBadRequestException extends DomainException
{
    public $message = "Złe zapytanie.";
}
