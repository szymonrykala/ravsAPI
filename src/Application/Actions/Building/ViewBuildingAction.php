<?php
declare(strict_types=1);

namespace App\Application\Actions\Building;

use Psr\Http\Message\ResponseInterface as Response;

use App\Domain\Image\Image;
use App\Domain\Address\Address;


class ViewBuildingAction extends BuildingAction
{
    /**
     * {@inheritdoc}
     */
    protected function action(): Response
    {
        $buildingId = (int) $this->resolveArg('building_id');
        $addressId = (int) $this->resolveArg('address_id');

        $building = $this->buildingRepository->byIdAndAddressId($buildingId, $addressId);



        $imageKey = Image::class.$building->imageId;
        $addressKey = Address::class.$building->addressId;
        // key = className + id of resource

        if($this->cache->contain($imageKey))
        {
            $image = $this->cache->get($imageKey);
        } else {
            $image = $this->imageRepository->byId($building->imageId);
            $this->cache->set($imageKey, $image );
        }

        if($this->cache->contain($addressKey))
        {
            $address = $this->cache->get($addressKey);
        } else {
            $address = $this->addressRepository->byId($building->addressId);
            $this->cache->set($addressKey, $address );
        }
        

        $building->image = $image;
        $building->address = $address;
        

        $this->logger->info("Building id ".$building->id." was viewed.");

        return $this->respondWithData($building);
    }
}
