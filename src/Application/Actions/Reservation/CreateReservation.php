<?php

declare(strict_types=1);

namespace App\Application\Actions\Reservation;

use App\Domain\Exception\DomainBadRequestException;
use App\Domain\Reservation\IReservationRepository;
use App\Domain\Reservation\Policy\ReservationCreatePolicy;
use App\Utils\JsonDateTime;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Log\LoggerInterface;
use Slim\Exception\HttpBadRequestException;

class CreateReservation extends ReservationAction
{

    private ReservationCreatePolicy $createPolicy;

    public function __construct(
        LoggerInterface $logger,
        IReservationRepository $reservations,
        ReservationCreatePolicy $createPolicy
    ) {
        parent::__construct($logger, $reservations);
        $this->createPolicy = $createPolicy;
    }

    /**
     * {@inheritdoc}
     * @throws DomainBadRequestException
     */
    protected function action(): Response
    {
        $form = $this->getFormData();
        $roomId = (int) $this->resolveArg('room_id', FALSE);
        $buildingId = (int) $this->resolveArg('building_id', FALSE);

        if (!$roomId) {
            if (isset($form->roomId)) $roomId = $form->roomId;
            else throw new HttpBadRequestException(
                $this->request,
                'If using `/reservations` endpoint, you must specify `roomId` in request body'
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

        // send email that reservation was created

        return $this->respondWithData($id, 201);
    }
}
