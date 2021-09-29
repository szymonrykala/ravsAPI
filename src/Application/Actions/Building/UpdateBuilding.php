<?php
declare(strict_types=1);

namespace App\Application\Actions\Building;

use App\Utils\JsonDateTime;
use Psr\Http\Message\ResponseInterface as Response;
use Slim\Exception\HttpNotFoundException;


class UpdateBuilding extends BuildingAction
{
    /**
     * {@inheritdoc}
     */
    protected function action(): Response
    {
        $addressId = (int) $this->resolveArg('address_id');
        $buildingId = (int) $this->resolveArg('building_id');

        $buildingParam = ['id' => $buildingId ];

        if(isset($addressId)) $buildingParam['address'] = $addressId;

        $result = $this->buildingRepository->where($buildingParam)->all();
        $building = array_pop($result);


        if(! isset($building)) throw new HttpNotFoundException(
            $this->request,
            "Specified building '${buildingId}' is not exist in given address '${addressId}'"
        );

        $form = $this->getFormData();

        if(isset($form->closeTime)) $form->closeTime = new JsonDateTime($form->closeTime);
        if(isset($form->openTime)) $form->openTime = new JsonDateTime($form->openTime);

        $building->update($form);

        // $this->buildingRepository->save($building);


        $this->logger->info("Building id $buildingId was updated.");

        return $this->respondWithData();
    }
}
