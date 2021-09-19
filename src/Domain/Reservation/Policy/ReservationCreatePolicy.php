<?php

declare(strict_types=1);


namespace App\Domain\Reservation\Policy;

use App\Domain\Reservation\Reservation;
use stdClass;

final class ReservationCreatePolicy extends ReservationPolicy
{

    /**
     * {@inheritdoc}
     */
    public function __invoke(stdClass $form, ?Reservation $originalReservation = NULL): void
    {
        $this->user = $this->userRepository->byId($form->user);

        // if building is not specified, POST '/reservations' endpoint
        if (!$form->building) {
            $this->roomRepository->withBuilding();
        }

        $this->room = $this->roomRepository->byId($form->room);

        // if building is not specified, POST '/reservations' endpoint
        $this->building = $form->building ?
            $this->buildingRepository->byId($form->building)
            : $this->room->building;


        // $this->form = $form;
        $this->start = $form->plannedStart;
        $this->end = $form->plannedEnd;

        $this->roomCannotBeBlocked();
        $this->roomBelongsToBuilding();
        $this->reservationHasFutureTime();
        $this->reservationTimeSlotLengthIsOk();
        $this->reservationWhenBuildingIsOpen();
        $this->noCrossingReservationWasMade();
    }
}
