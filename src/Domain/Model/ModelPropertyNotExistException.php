<?php
declare(strict_types=1);

namespace App\Domain\Model;

use App\Domain\Exception\DomainBadRequestException;


class ModelPropertyNotExistException extends DomainBadRequestException
{
    function __construct(string $name){
        $this->message = "The property '${name}' is not exist, or You can not update it in this way.";
    }

}
