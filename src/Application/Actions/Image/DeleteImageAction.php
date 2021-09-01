<?php
declare(strict_types=1);

namespace App\Application\Actions\Image;

use Slim\Exception\HttpBadRequestException;
use Psr\Http\Message\ResponseInterface as Response;

use App\Domain\Image\ImageDeleteException;

class DeleteImageAction extends ImageAction
{
    /**
     * {@inheritdoc}
     */
    protected function action(): Response
    {
        $imageId = (int) $this->resolveArg('id');
        try{
            $this->imageRepository->deleteById($imageId);
        }catch(ImageDeleteException $e){
            throw new HttpBadRequestException($this->request,$e->getMessage());
        }

        $this->logger->info("Image of id `${imageId}` has been deleted.");

        return $this->respondWithData();
    }
}
