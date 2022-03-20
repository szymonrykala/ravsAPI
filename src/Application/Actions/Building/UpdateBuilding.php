<?php

declare(strict_types=1);

namespace App\Application\Actions\Building;

use App\Domain\Building\Validation\UpdateValidator;
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
        $addressId = (int) $this->resolveArg($this::ADDRESS_ID);
        $buildingId = (int) $this->resolveArg($this::BUILDING_ID);

        $buildingParam = ['id' => $buildingId];

        if (isset($addressId))
            $buildingParam['address'] = $addressId;

        $result = $this->buildingRepository->where($buildingParam)->all();
        $building = array_pop($result); // first result 

        // it there is not such building - query result is empty
        if (!isset($building)) {
            $this->logger->warning("No building found (address=${addressId}, building=${buildingId}) to update");

            throw new HttpNotFoundException(
                $this->request,
                "Specified building '${buildingId}' is not exist in given address '${addressId}'"
            );
        }

        $form = $this->getFormData();


        $validator = new UpdateValidator();
        $validator->validateForm($form);

        // assign proper type for time objects
        if (isset($form->closeTime)) $form->closeTime = new JsonDateTime($form->closeTime);
        if (isset($form->openTime)) $form->openTime = new JsonDateTime($form->openTime);

        $building->update($form);

        $this->buildingRepository->save($building);


        $this->logger->info("Building id $buildingId was updated.");

        return $this->respondWithData();
    }
}
