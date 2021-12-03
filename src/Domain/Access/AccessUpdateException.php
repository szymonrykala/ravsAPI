<?php
declare(strict_types=1);

namespace App\Domain\Access;

use App\Domain\Exception\DomainForbiddenOperationException;

class AccessUpdateException extends DomainForbiddenOperationException
{
    public $message = 'Nie można zaktualizować klasy dostępu.';
}
