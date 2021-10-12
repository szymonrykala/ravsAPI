<?php

declare(strict_types=1);

namespace App\Domain\Exception;

class DomainForbiddenOperationException extends DomainException
{
    public $message = "Operacja którą chcesz wykonać jest niedozwolona.";
}
