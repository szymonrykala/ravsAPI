<?php
declare(strict_types=1);

namespace App\Application\Actions\Room;

use App\Domain\Room;

use App\Application\Actions\Action;
use App\Domain\Room\RoomRepositoryInterface;
use App\Domain\Building\IBuildingRepository;
use App\Domain\Image\ImageRepositoryInterface;
use App\Domain\Request\RequestRepositoryInterface;

use Psr\Log\LoggerInterface;
use App\Application\Actions\IActionCache;


abstract class RoomAction extends Action
{
    protected $roomRepository;    
    protected $imageRepository;    
    protected $buildingRepository;

    protected IActionCache $cache;

    /**
     * @param LoggerInterface $logger
     * @param RequestRepositoryInterface $requestRepository
     * @param IActionCache $cache
     * @param RoomRepositoryInterface $userRepository
     * @param IBuildingRepository $userRepository
     * @param ImageRepositoryInterface $imageRepository
     */
    public function __construct(
        LoggerInterface $logger,
        RequestRepositoryInterface $requestRepo,
        IActionCache $cache,
        RoomRepositoryInterface $roomRepository,
        IBuildingRepository $buildingRepository,
        ImageRepositoryInterface $imageRepository
    ) {
        parent::__construct($logger, $requestRepo);
        $this->cache = $cache;

        $this->roomRepository = $roomRepository;
        $this->buildingRepository = $buildingRepository;
        $this->imageRepository = $imageRepository;
    }
}

