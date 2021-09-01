<?php
declare(strict_types=1);

namespace App\Application\Actions\Image;

use Slim\Exception\HttpBadRequestException;
use Psr\Http\Message\ResponseInterface as Response;

class UploadImageAction extends ImageAction
{
    /**
     * {@inheritdoc}
     */
    protected function action(): Response
    {
        $files = $this->request->getUploadedFiles();
        if(empty($files)){
            throw new HttpBadRequestException($this->request,'No uploaded image found.');
        }
        $file = array_pop($files);

        if ($file->getError() !== UPLOAD_ERR_OK) {
            throw new HttpBadRequestException($this->request, 'Error has occured while uploading Image.');
        }

        $imageId = $this->imageRepository->save($file);

        $this->logger->info("Image of id `${imageId}` was uploaded.");

        return $this->respondWithData($imageId);
    }
}
