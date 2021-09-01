<?php
declare(strict_types=1);

namespace App\Application\Actions\Building;

use Psr\Http\Message\ResponseInterface as Response;


class DeleteBuildingAction extends BuildingAction
{
    /**
     * {@inheritdoc}
     */
    protected function action(): Response
    {
        $buildingId = (int) $this->resolveArg('building_id');
        $addressId = (int) $this->resolveArg('address_id');

        $this->buildingRepository->byIdAndAddressId($buildingId, $addressId);
        $this->buildingRepository->deleteById( (int) $buildingId);

        $this->logger->info("Building id ${buildingId} was deleted.");

        return $this->respondWithData();
    }
}
