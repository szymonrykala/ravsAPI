<?php
declare(strict_types=1);

namespace App\Application\Actions\Image;

use Psr\Http\Message\ResponseInterface as Response;

class ViewImageAction extends ImageAction
{
    /**
     * {@inheritdoc}
     */
    protected function action(): Response
    {
        $imageId = (int) $this->resolveArg('id');
        $image = $this->imageRepository->byId($imageId);

        $this->logger->info("Image of id `${imageId}` was viewed.");

        return $this->respondWithData($image);
    }
}
