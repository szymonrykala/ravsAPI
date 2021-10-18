<?php
declare(strict_types=1);

namespace App\Application\Actions\Image;

use App\Application\Actions\Action;
use App\Domain\Image\IImageRepository;
use Psr\Log\LoggerInterface;


abstract class ImageAction extends Action
{
    protected IImageRepository $imageRepository;

    /**
     * @param LoggerInterface $logger
     * @param IImageRepository $imageRepository
     */
    public function __construct(
        LoggerInterface $logger,
        IImageRepository $imageRepository
    ) {
        parent::__construct($logger);
        $this->imageRepository = $imageRepository;
    }
}

