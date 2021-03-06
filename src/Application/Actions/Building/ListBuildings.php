<?php
declare(strict_types=1);

namespace App\Application\Actions\Building;

use Psr\Http\Message\ResponseInterface as Response;



class ListBuildings extends BuildingAction
{
    /**
     * {@inheritdoc}
     */
    protected function action(): Response
    {
        $addressId = $this->resolveArg($this::ADDRESS_ID);

        $buildings = $this->buildingRepository
                            ->where(['address' => $addressId])
                            ->orderBy('name','ASC')
                            ->all();


        $this->logger->info("Buildings list for address id $addressId was viewed.");

        return $this->respondWithData($buildings);
    }
}
