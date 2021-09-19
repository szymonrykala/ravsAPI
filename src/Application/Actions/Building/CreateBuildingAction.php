<?php
declare(strict_types=1);

namespace App\Application\Actions\Building;

use App\Utils\JsonDateTime;
use Psr\Http\Message\ResponseInterface as Response;


class CreateBuildingAction extends BuildingAction
{
    /**
     * {@inheritdoc}
     */
    protected function action(): Response
    {
        $addressId = (int) $this->resolveArg('address_id');

        $form = $this->getFormData();


        $createdBuildingId = $this->buildingRepository->create(
            $form->name,
            new JsonDateTime($form->open_time),
            new JsonDateTime($form->close_time),
            $addressId
        );


        $this->logger->info("Building id $createdBuildingId was created.");

        return $this->respondWithData($createdBuildingId);
    }
}