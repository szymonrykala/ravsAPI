<?php
declare(strict_types=1);

namespace App\Application\Actions\Image;

use App\Application\Actions\Action;
use App\Domain\Image\ImageRepositoryInterface;
use Psr\Log\LoggerInterface;
use App\Domain\Request\RequestRepositoryInterface;

abstract class ImageAction extends Action
{
    protected ImageRepositoryInterface $imageRepository;

    /**
     * @param LoggerInterface $logger
     * @param ImageRepositoryInterface $imageRepository
     */
    public function __construct(
        LoggerInterface $logger,
        ImageRepositoryInterface $imageRepository
    ) {
        parent::__construct($logger);
        $this->imageRepository = $imageRepository;
    }

    protected function getIdentifier(): string
    {
        $path = $this->request->getUri()->getPath();
        return str_replace('/','',$path);
    }
}

