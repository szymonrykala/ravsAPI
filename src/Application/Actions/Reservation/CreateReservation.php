<?php

declare(strict_types=1);

namespace App\Application\Actions\Reservation;

use App\Domain\Exception\DomainBadRequestException;
use App\Domain\Reservation\Policy\ReservationCreatePolicy;
use App\Domain\Reservation\Validation\CreateValidator;
use App\Utils\JsonDateTime;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Log\LoggerInterface;
use Slim\Exception\HttpBadRequestException;

class CreateReservation extends ReservationAction
{
    private ReservationCreatePolicy $createPolicy;


    public function __construct(ContainerInterface $di)
    {
        parent::__construct($di);
        $this->createPolicy = $di->get(ReservationCreatePolicy::class);
    }

    /**
     * {@inheritDoc}
     * @throws DomainBadRequestException
     */
    protected function action(): Response
    {
        $form = $this->getFormData();

        $validator = new CreateValidator();
        $validator->validateForm($form);

        $roomId = (int) $this->resolveArg($this::ROOM_ID, FALSE);
        $buildingId = (int) $this->resolveArg($this::BUILDING_ID, FALSE);

        if (!$roomId) {
            if (isset($form->roomId)) $roomId = $form->roomId;
            else throw new HttpBadRequestException(
                $this->request,
                'Jeżeli używasz `/reservations`, musisz podać `roomId` w ładuknu wiadomości.'
            );
        }

        $form->user = $this->session->userId;
        $form->room = $roomId;
        $form->building = $buildingId;
        $form->plannedStart = new JsonDateTime($form->plannedStart);
        $form->plannedEnd = new JsonDateTime($form->plannedEnd);



        $this->createPolicy->__invoke($form);

        $id = $this->reservations->create(
            $form->title,
            $form->description,
            $roomId,
            $this->session->userId,
            $form->plannedStart,
            $form->plannedEnd
        );

        return $this->respondWithData($id, 201);
    }
}
