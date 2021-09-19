<?php

declare(strict_types=1);

namespace App\Application\Actions\Building;

use Psr\Http\Message\ResponseInterface as Response;


class ViewBuildingAction extends BuildingAction
{
    /**
     * {@inheritdoc}
     */
    protected function action(): Response
    {
        $buildingId = (int) $this->resolveArg('building_id');
        $addressId = (int) $this->resolveArg('address_id');

        $building = $this->buildingRepository
            ->withAddress()
            ->where([
                ':id' => $buildingId,
                ':address_id' => $addressId
            ])->one();



        $this->logger->info("Building id " . $building->id . " was viewed.");

        return $this->respondWithData($building);
    }
}
