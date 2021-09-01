<?php
declare(strict_types=1);

namespace App\Application\Actions\Building;

use App\Domain\Building;

use App\Application\Actions\Action;
use App\Domain\Building\IBuildingRepository;
use App\Domain\Address\IAddressRepository;
use App\Domain\Image\ImageRepositoryInterface;
use App\Domain\Request\RequestRepositoryInterface;

use Psr\Log\LoggerInterface;
use App\Application\Actions\IActionCache;


abstract class BuildingAction extends Action
{
    protected $buildingRepository;    
    protected $imageRepository;    
    protected $addressRepository;

    protected IActionCache $cache;

    /**
     * @param LoggerInterface $logger
     * @param RequestRepositoryInterface $requestRepository
     * @param IActionCache $cache
     * @param IBuildingRepository $buildingRepository
     * @param ImageRepositoryInterface $imageRepository
     * @param IAddressRepository $accessRepository
     */
    public function __construct(
        LoggerInterface $logger,
        RequestRepositoryInterface $requestRepo,
        IActionCache $cache,
        IBuildingRepository $buildingRepository,
        ImageRepositoryInterface $imageRepository,
        IAddressRepository $addressRepository
    ) {
        parent::__construct($logger, $requestRepo);
        $this->cache = $cache;

        $this->buildingRepository = $buildingRepository;
        $this->imageRepository = $imageRepository;
        $this->addressRepository = $addressRepository;
    }
}