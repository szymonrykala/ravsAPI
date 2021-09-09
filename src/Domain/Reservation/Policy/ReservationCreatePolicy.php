<?php

declare(strict_types=1);


namespace App\Domain\Reservation\Policy;

use stdClass;

final class ReservationCreatePolicy extends ReservationPolicy
{

    /**
     * {@inheritdoc}
     */
    public function __invoke(stdClass $form): void {
        $this->user = $this->userRepository->byId($form->user);
        $this->room = $this->roomRepository->byId($form->room);
        $this->building = $this->buildingRepository->byId($form->building);

        $this->form = $form;

        $this->checkRoom();
        $this->checkTimePolicies();
    }

}
