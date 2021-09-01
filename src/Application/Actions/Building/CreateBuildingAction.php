<?php
declare(strict_types=1);

namespace App\Application\Actions\Building;

use Psr\Http\Message\ResponseInterface as Response;

use App\Domain\Image\Image;
use App\Domain\Address\Address;
use DateTime;

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
            new DateTime($form->open_time),
            new DateTime($form->close_time),
            $addressId
        );


        $this->logger->info("Building id $createdBuildingId was created.");

        return $this->respondWithData($createdBuildingId);
    }
}