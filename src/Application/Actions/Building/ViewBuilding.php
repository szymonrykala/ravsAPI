<?php

declare(strict_types=1);

namespace App\Application\Actions\Building;

use Psr\Http\Message\ResponseInterface as Response;


class ViewBuilding extends BuildingAction
{
    /**
     * {@inheritdoc}
     */
    protected function action(): Response
    {
        $buildingId = $this->resolveArg('building_id');
        $addressId = $this->resolveArg('address_id', FALSE);

        $params = ['id' => (int) $buildingId];

        $addressId && $params['address'] = (int) $addressId;

        $building = $this->buildingRepository
            ->withAddress()
            ->where($params)
            ->one();



        $this->logger->info("Building id " . $building->id . " was viewed.");

        return $this->respondWithData($building);
    }
}
