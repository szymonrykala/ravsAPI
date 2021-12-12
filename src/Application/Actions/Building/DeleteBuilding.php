<?php

declare(strict_types=1);

namespace App\Application\Actions\Building;

use Psr\Http\Message\ResponseInterface as Response;


class DeleteBuilding extends BuildingAction
{
    /**
     * {@inheritdoc}
     */
    protected function action(): Response
    {
        $buildingId = (int) $this->resolveArg($this::BUILDING_ID);
        $addressId = (int) $this->resolveArg($this::ADDRESS_ID);

        $building = $this->buildingRepository->where([
            'id' => $buildingId,
            'address' => $addressId
        ])->one();


        $this->buildingRepository->delete($building);

        $this->logger->info("Building id ${buildingId} was deleted.");

        return $this->respondWithData();
    }
}
