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
        $imageId = (int) $this->resolveArg($this::IMAGE_ID);

        $image = $this->imageRepository->byId($imageId);
        $this->imageRepository->delete($image);

        $this->logger->info("Image of id `${imageId}` has been deleted.");

        return $this->respondWithData();
    }
}
