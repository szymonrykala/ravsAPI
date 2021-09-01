<?php
declare(strict_types=1);

namespace App\Application\Actions\Image;

use Psr\Http\Message\ResponseInterface as Response;


class ListAllImagesAction extends ImageAction
{
    /**
     * {@inheritdoc}
     */
    protected function action(): Response
    {
        $images = $this->imageRepository->all();

        $this->logger->info("All Images has been viewed.");

        return $this->respondWithData($images);
    }
}


