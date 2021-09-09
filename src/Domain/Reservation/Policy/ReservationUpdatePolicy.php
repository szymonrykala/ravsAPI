<?php

declare(strict_types=1);


namespace App\Domain\Reservation\Policy;

use App\Domain\Reservation\Reservation;
use DateTime;
use stdClass;

final class ReservationUpdatePolicy extends ReservationPolicy
{

    public Reservation $reservation;

    /**
     * {@inheritdoc}
     */
    public function __invoke(stdClass $form): void {
        $this->user = $this->userRepository->byId($form->user);
        $this->room = $this->roomRepository->byId($form->roomId);
        $this->building = $this->buildingRepository->byId($this->room->buildingId);

        $this->form = $form;

        $this->checkUpdateAbility();
        $this->checkRoom();
        $this->checkTimePolicies($this->reservationId);
    }

    private function checkUpdateAbility():void{
        $confirmedTimeSlotUpdate = $this->reservation->confirmed 
                && (isset($this->form->plannedEnd) || isset($this->form->plannedStart));
    
        if($confirmedTimeSlotUpdate) 
            throw new ConfirmedReservationTimeUdpateException();
    }
}
