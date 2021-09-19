<?php
declare(strict_types=1);

namespace App\Application\Actions\Image;

use Slim\Exception\HttpBadRequestException;
use Psr\Http\Message\ResponseInterface as Response;

use App\Domain\Image\ImageDeleteException;
use Slim\Exception\HttpForbiddenException;

class DeleteImageAction extends ImageAction
{
    /**
     * {@inheritdoc}
     */
    protected function action(): Response
    {
        $imageId = (int) $this->resolveArg('id');

        $image = $this->imageRepository->byId($imageId);
        $this->imageRepository->delete($image);

        $this->logger->info("Image of id `${imageId}` has been deleted.");

        return $this->respondWithData();
    }
}
