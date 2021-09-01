<?php
declare(strict_types=1);

namespace App\Application\Actions\Building;

use Psr\Http\Message\ResponseInterface as Response;

use App\Domain\Image\Image;
use App\Domain\Address\Address;


class ListBuildingsAction extends BuildingAction
{
    /**
     * {@inheritdoc}
     */
    protected function action(): Response
    {
        $addressId = $this->resolveArg('address_id');

        $buildings = $this->buildingRepository
                            ->where(['address' => $addressId])
                            ->all();

        foreach($buildings as $building)
        {   
            $imageKey = Image::class.$building->imageId;
            // $addressKey = Address::class.$building->getAddressId();

            if($this->cache->contain($imageKey))
            {
                $image = $this->cache->get($imageKey);
            } else {
                $image = $this->imageRepository->byId($building->imageId);
                $this->cache->set($imageKey, $image );    
            }   

            // if($this->cache->contain($addressKey))
            // {
            //     $image = $this->cache->get($addressKey);
            // } else {
            //     $image = $this->addressRepository->byId($building->getImageId());
            //     $this->cache->set($addressKey, $image );
            // }

            $building->image = $image;    
        }

        $this->logger->info("Buildings list for address id $addressId was viewed.");

        return $this->respondWithData($buildings);
    }
}
