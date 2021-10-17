<?php

declare(strict_types=1);

namespace App\Domain\Exception;


class DomainUnauthenticatedException extends DomainException
{
    public $message = 'Unauthenticated.';
}
