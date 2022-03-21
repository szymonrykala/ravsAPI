<?php
declare(strict_types=1);

namespace App\Domain\Image;

use App\Domain\Exception\DomainBadRequestException;

class ImageSizeExceededException extends DomainBadRequestException
{
    public function __construct($size){
        parent::__construct(
            'Przekroczony rozmiar zdjęcia. Maksymalny rozmiar to '.($size/1000).' kB'
        );
    }
}
