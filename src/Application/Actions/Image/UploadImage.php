<?php

declare(strict_types=1);

namespace App\Application\Actions\Image;

use App\Domain\Model\Model;
use Slim\Exception\HttpBadRequestException;
use Psr\Http\Message\ResponseInterface as Response;



class UploadImage extends ImageAction
{

    /**
     * {@inheritdoc}
     */
    protected function action(): Response
    {
        $files = $this->request->getUploadedFiles();

        if (empty($files)) {
            $this->logger->warning('No image was found while uploading');
            throw new HttpBadRequestException($this->request, 'Nie znaleziono żadnego obrazu');
        }

        $file = array_pop($files);
        if ($file->getError() !== UPLOAD_ERR_OK) {
            $this->logger->error('Error while processing the request');
            throw new HttpBadRequestException($this->request, 'Wystąpił błąd podczas ładowania obrazu');
        }

        [$repo, $objectId] = $this->getPropperObjectSet();
        /** @var Model $object */
        $object = $repo->byId((int)$objectId); // get object which image is uploading

        $imageId = $this->imageRepository->save($file); // save uploaded image

        $object->imageId = $imageId; // assign new image to object
        $repo->save($object); // save the image

        $this->imageRepository->delete($object->image); // delete the old image

        $this->logger->info("Image of resource id `${objectId}` was uploaded.");

        return $this->respondWithData($imageId, 201);
    }


}
