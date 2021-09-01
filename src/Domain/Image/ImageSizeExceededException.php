<?php
declare(strict_types=1);

namespace App\Domain\Image;

use App\Domain\Exception\DomainException;

class ImageSizeExceededException extends DomainException
{
    public function __construct($size){
        parent::__construct(
            'Image size can not exceed '.($size/1000).' kB'
        );
    }
}
