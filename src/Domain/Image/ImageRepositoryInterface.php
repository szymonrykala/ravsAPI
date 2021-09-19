<?php
declare(strict_types=1);

namespace App\Domain\Image;

use Psr\Http\Message\UploadedFileInterface;
use App\Domain\Model\RepositoryInterface;



interface ImageRepositoryInterface extends RepositoryInterface
{

    /**
     * @param UploadedFileInterface $file
     * @return int $id of saved image
     * 
     * @throws ImageSizeExceededException
     */
    public function save(UploadedFileInterface $file):int;

}
