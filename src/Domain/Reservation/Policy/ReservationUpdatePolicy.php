<?php

declare(strict_types=1);


namespace App\Domain\Reservation\Policy;

use App\Domain\Reservation\Reservation;
use App\Utils\JsonDateTime;
use stdClass;

final class ReservationUpdatePolicy extends ReservationPolicy
{

    public Reservation $reservation;

    /**
     * {@inheritdoc}
     */
    public function __invoke(stdClass $form, ?Reservation $originalReservation = NULL): void
    {
        if ($originalReservation === NULL) throw new \Exception('Reservation is required');

        $this->reservation = $originalReservation;

        // $this->form = $form;
        $this->reservationIsNotOver();
        $this->updateMaxOneDayBefore();

        $timeUpdate = isset($form->plannedEnd) || isset($form->plannedStart);
        $placeUpdate = isset($form->buildingId) || isset($form->roomId);



        if ($placeUpdate || $timeUpdate) {
            $this->room = isset($form->roomId) ? $this->roomRepository->byId($form->roomId)
                : $originalReservation->room;

            $this->building = $this->buildingRepository->byId($this->room->buildingId);

            if ($placeUpdate) {
                $this->roomBelongsToBuilding();
                $this->roomCannotBeBlocked();
            }

            if ($timeUpdate) {
                $this->start = $form->plannedStart ?? $originalReservation->plannedStart;
                $this->end = $form->plannedEnd ?? $originalReservation->plannedEnd;

                $this->reservationHasFutureTime();
                $this->reservationTimeSlotLengthIsOk();
                $this->reservationWhenBuildingIsOpen();
            }

            $this->noCrossingReservationWasMade($originalReservation->id);
        }
    }

    /**
     * @throw 
     * @return void
     */
    private function updateMaxOneDayBefore(): void
    {
        /** @var DateInterval $timeDiff*/
        $timeDiff = $this->reservation->plannedStart->diff(new JsonDateTime('now'));

        // if reservation start time is not earlier than current time
        if ($timeDiff->invert === 0) {

            $moreThanOneDayBefore = $timeDiff->d = 0 && $timeDiff->h < 24;
            if (!$moreThanOneDayBefore) throw new TooLateReservationUpdateException();
        }
    }

    private function reservationIsNotOver(): void
    {
        if ($this->reservation->plannedStart < new JsonDateTime('now'))
            throw new PassedReservationUpdateException();
    }
}
