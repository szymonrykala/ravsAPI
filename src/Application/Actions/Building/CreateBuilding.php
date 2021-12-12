<?php
declare(strict_types=1);

namespace App\Application\Actions\Building;

use App\Utils\JsonDateTime;
use Psr\Http\Message\ResponseInterface as Response;


class CreateBuilding extends BuildingAction
{
    /**
     * {@inheritdoc}
     */
    protected function action(): Response
    {
        $addressId = (int) $this->resolveArg($this::ADDRESS_ID);

        $form = $this->getFormData();


        $createdBuildingId = $this->buildingRepository->create(
            $form->name,
            new JsonDateTime($form->openTime),
            new JsonDateTime($form->closeTime),
            $addressId
        );


        $this->logger->info("Building id $createdBuildingId was created.");

        return $this->respondWithData($createdBuildingId);
    }
}