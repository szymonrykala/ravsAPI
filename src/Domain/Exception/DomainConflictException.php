<?php

declare(strict_types=1);

namespace App\Domain\Exception;

class DomainConflictException extends DomainException
{
    public $message = "System napotkał konflikt i nie może wykonać tej operacji.";
}
