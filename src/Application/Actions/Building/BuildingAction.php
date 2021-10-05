<?php
declare(strict_types=1);

namespace App\Application\Actions\Building;


use App\Application\Actions\Action;
use App\Domain\Building\IBuildingRepository;

use Psr\Log\LoggerInterface;


abstract class BuildingAction extends Action
{
    protected IBuildingRepository $buildingRepository;      

    /**
     * @param LoggerInterface logger
     * @param IBuildingRepository buildingRepository
     */
    public function __construct(
        LoggerInterface $logger,
        IBuildingRepository $buildingRepository
    ) {
        parent::__construct($logger);

        $this->buildingRepository = $buildingRepository;
    }
}