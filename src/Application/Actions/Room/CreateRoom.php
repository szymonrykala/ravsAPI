<?php

declare(strict_types=1);

namespace App\Application\Actions\Room;

use App\Domain\Building\IBuildingRepository;
use App\Domain\Room\Validation\CreateValidator;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface as Response;



class CreateRoom extends RoomAction
{
    private IBuildingRepository $buildingRepository;

    public function __construct(ContainerInterface $di)
    {
        parent::__construct($di);

        $this->buildingRepository = $di->get(IBuildingRepository::class);
    }

    /**
     * {@inheritDoc}
     */
    protected function action(): Response
    {
        $buildingId = (int) $this->resolveArg($this::BUILDING_ID);
        $addressId = (int) $this->resolveArg($this::ADDRESS_ID);

        $form = $this->getformData();

        $validator = new CreateValidator();
        $validator->validateForm($form);

        $this->buildingRepository->where([
            'id' => $buildingId,
            'address' => $addressId
        ])->one();

        $newRoomId = $this->roomRepository->create(
            $form->name,
            $buildingId,
            $form->roomType,
            $form->seatsCount,
            $form->floor
        );

        $this->logger->info("room id=${newRoomId} has been created");

        return $this->respondWithData($newRoomId);
    }
}
