<?php

declare(strict_types=1);

namespace App\Application\Actions\Image;

use Psr\Http\Message\ResponseInterface as Response;


class DeleteImage extends ImageAction
{
    /**
     * {@inheritdoc}
     */
    protected function action(): Response
    {
        [$repo, $objectId] = $this->getPropperObjectSet();

        /** @var Model $object */
        $object = $repo->byId((int)$objectId);

        $repo->setDefaultImage($object);
        $this->logger->info('Default image has been set');
        
        $this->imageRepository->delete($object->image);
        $this->logger->info('Image with id `' . $object->image->id . '` has been deleted.');

        return $this->respondWithData();
    }
}
