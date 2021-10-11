<?php

declare(strict_types=1);

namespace App\Application\Actions\Image;

use Psr\Http\Message\ResponseInterface as Response;
use Slim\Psr7\Factory\StreamFactory;


final class ViewImage extends ImageAction
{
    /**
     * {@inheritDoc}
     */
    protected function action(): Response
    {
        $imageId = (int) $this->resolveArg($this::IMAGE_ID);
        
        $fileStream = $this->imageRepository->viewImageFile($imageId);
        
        $this->logger->info("Image of id `${imageId}` was viewed.");

        return $this->response
            ->withBody($fileStream)
            ->withHeader('Content-type', 'image/png');
    }
}
