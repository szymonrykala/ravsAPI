<?php

declare(strict_types=1);

namespace App\Domain\Exception;

class DomainResourceNotFoundException extends DomainException
{
    public $message = "Żądany zasób nie istnieje.";
}
