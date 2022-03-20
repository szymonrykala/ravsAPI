<?php
declare(strict_types=1);

namespace App\Application\Actions\Building;


use App\Application\Actions\Action;
use App\Domain\Building\IBuildingRepository;
use Psr\Log\LoggerInterface;


abstract class BuildingAction extends Action
{

    public function __construct(
        LoggerInterface $logger,
        protected IBuildingRepository $buildingRepository
    ) {
        parent::__construct($logger);
    }
}