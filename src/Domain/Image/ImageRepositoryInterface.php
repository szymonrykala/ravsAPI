<?php

declare(strict_types=1);

namespace App\Domain\Image;

use Psr\Http\Message\UploadedFileInterface;
use App\Domain\Model\RepositoryInterface;



interface ImageRepositoryInterface extends RepositoryInterface
{

    /**
     * Saves the uploaded image with path identifier
     * @throws ImageSizeExceededException
     */
    public function save(UploadedFileInterface $file, string $prefix = ''): int;

    /**
     * Read all images containing given prefix
     */
    public function allLike(string $prefix): array;
}
