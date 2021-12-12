<?php

declare(strict_types=1);

namespace App\Domain\Image;

use App\Domain\Exception\DomainBadRequestException;


class FileTypeException extends DomainBadRequestException
{
    public $message = 'Przesłany plik nie jest obrazem';
}
