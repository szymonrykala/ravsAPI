<?php

declare(strict_types=1);

namespace App\Application\Actions\Address;

use App\Domain\Address\IAddressRepository;
use App\Domain\Building\IBuildingRepository;
use App\Domain\Room\IRoomRepository;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Log\LoggerInterface;


class ViewResourcesMap extends AddressAction
{

    public function __construct(
        LoggerInterface $logger,
        IAddressRepository $addressRepository,
        private IBuildingRepository $buildingRepository,
        private IRoomRepository $roomRepository
    ) {
        parent::__construct($logger, $addressRepository);
    }


    /**
     * {@inheritdoc}
     */
    protected function action(): Response
    {
        $map = [];

        $map = $this->addressRepository->orderBy('street', 'ASC')->all();

        /**
         * building block like:
         * [
         *      - address:
         *          - building:
         *              - room,
         *              - room,
         *              - ...
         *          - building
         *              - ...
         *          - ...
         *      - address:
         *          - ...
         * ]
         */
        foreach ($map as &$address) {
            $address = [
                'id' => $address->id,
                'name' => $address->street . ' ' . $address->number,
                'href' => '/addresses/'.$address->id,
                'buildings' => $this->buildingRepository
                    ->where(['address' => $address->id])
                    ->orderBy('name', 'ASC')
                    ->all()
            ];

            foreach ($address['buildings'] as &$building) {
                $building = [
                    'id' => $building->id,
                    'name' => $building->name,
                    'href' => $address['href'].'/buildings/'.$building->id,
                    'rooms' => $this->roomRepository
                        ->where(['building' => $building->id])
                        ->orderBy('name', 'ASC')
                        ->all()
                ];

                foreach ($building['rooms'] as &$room) {
                    $room = [
                        'id' => $room->id,
                        'href' => $building['href'].'/rooms/'.$room->id,
                        'name' => $room->name
                    ];
                }
            }
        }

        $this->logger->info("Resources map has been viewed.");

        return $this->respondWithData($map);
    }
}
