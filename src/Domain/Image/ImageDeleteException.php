<?php
declare(strict_types=1);

namespace App\Domain\Image;

use App\Domain\Exception\DomainForbiddenOperationException;

class ImageDeleteException extends DomainForbiddenOperationException
{
    public function __construct($file){
        parent::__construct(
            "Image `${file}` could not be deleted"
        );
    }
}
