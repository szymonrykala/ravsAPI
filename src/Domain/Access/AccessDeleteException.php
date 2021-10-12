<?php
declare(strict_types=1);

namespace App\Domain\Access;

use App\Domain\Exception\DomainForbiddenOperationException;

class AccessDeleteException extends DomainForbiddenOperationException
{
    public $message = 'Nie udało się usunąć klasy dostępu.';
}
