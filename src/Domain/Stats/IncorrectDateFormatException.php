<?php
declare(strict_types=1);

namespace App\Domain\Stats;

use App\Domain\Exception\DomainBadRequestException;



class IncorrectDateFormatException extends DomainBadRequestException
{
    public $message = 'Podana data ma niepoprawny format.';
}