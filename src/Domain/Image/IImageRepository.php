<?php

declare(strict_types=1);

namespace App\Domain\Image;

use Psr\Http\Message\UploadedFileInterface;
use App\Domain\Model\IRepository;


interface IImageRepository extends IRepository
{

    /**
     * Saves the uploaded image with path identifier
     * @throws ImageSizeExceededException
     */
    public function save(UploadedFileInterface $file): int;

}
