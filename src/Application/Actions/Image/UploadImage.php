<?php

declare(strict_types=1);

namespace App\Application\Actions\Image;

use App\Domain\Building\IBuildingRepository;
use App\Domain\Model\Model;
use App\Domain\Room\IRoomRepository;
use App\Domain\User\IUserRepository;
use Slim\Exception\HttpBadRequestException;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Container\ContainerInterface;



class UploadImage extends ImageAction
{

    /**
     * {@inheritdoc}
     */
    protected function action(): Response
    {
        $files = $this->request->getUploadedFiles();

        if (empty($files)) {
            throw new HttpBadRequestException($this->request, 'Nie znaleziono żadnego obrazu');
        }
        $file = array_pop($files);

        if ($file->getError() !== UPLOAD_ERR_OK) {
            throw new HttpBadRequestException($this->request, 'Wystąpił błąd podczas ładowania obrazu');
        }

        [$repo, $objectId,$_] = $this->getPropperObjectSet();
        /** @var Model $object */
        $object = $repo->byId((int)$objectId);

        $imageId = $this->imageRepository->save($file);

        $object->imageId = $imageId;
        $repo->save($object);

        $this->imageRepository->delete($object->image);

        $this->logger->info("Image of id `${imageId}` was uploaded.");

        return $this->respondWithData($imageId, 201);
    }


}
