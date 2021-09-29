<?php

declare(strict_types=1);

namespace App\Application\Actions\Reservation;

use App\Domain\Exception\DomainBadRequestException;
use App\Domain\Reservation\IReservationRepository;
use App\Domain\Reservation\Policy\ReservationUpdatePolicy;
use Psr\Http\Message\ResponseInterface as Response;

use Psr\Log\LoggerInterface;
use App\Domain\Reservation\Reservation;
use App\Utils\JsonDateTime;
use Psr\Container\ContainerInterface;

class UpdateReservation extends ReservationAction
{

    private ReservationUpdatePolicy $updatePolicy;

    public function __construct(ContainerInterface $di)
    {
        parent::__construct($di);
        $this->updatePolicy = $di->get(ReservationUpdatePolicy::class);
    }

    /**
     * {@inheritDoc}
     * @throws DomainBadRequestException
     * 
     */
    protected function action(): Response
    {
        $form = $this->getFormData();
        $reservationId = (int) $this->resolveArg('reservation_id');



        foreach (['plannedStart', 'plannedEnd'] as $field)
            if (isset($form->$field))
                $form->$field = new JsonDateTime($form->$field);


        /** @var Reservation $reservation */
        $reservation = $this->reservations->byId($reservationId);

        $this->updatePolicy->__invoke($form, $reservation);

        $reservation->update($form);

        $this->reservations->save($reservation);


        // send email that reservation was created

        return $this->respondWithData();
    }
}
