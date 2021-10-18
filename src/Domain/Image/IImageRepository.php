<?php

declare(strict_types=1);

namespace App\Domain\Image;

use Psr\Http\Message\UploadedFileInterface;
use App\Domain\Model\IRepository;
use Slim\Psr7\Stream;

interface IImageRepository extends IRepository
{

    /**
     * Saves the uploaded image with path identifier
     * @throws ImageSizeExceededException
     */
    public function save(UploadedFileInterface $file): int;

    /**
     * returns stream content of the image file
     */
    public function viewImageFile(int $id):Stream;
}
