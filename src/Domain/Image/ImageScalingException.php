<?php

declare(strict_types=1);

namespace App\Domain\Image;

use App\Domain\Exception\DomainBadRequestException;


class ImageScalingException extends DomainBadRequestException
{
    public $message = 'Wystąpił błąd podczas skalowania obrazu.';
}
