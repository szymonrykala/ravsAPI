<?php
declare(strict_types=1);

namespace App\Domain\Model;

use App\Domain\Exception\DomainBadRequestException;


class ModelPropertyNotExistException extends DomainBadRequestException
{
    function __construct(string $name){
        $this->message = "Właściwość '${name}' nie istnieje, lub nie można zaktualizować jej w taki sposób.";
    }

}
