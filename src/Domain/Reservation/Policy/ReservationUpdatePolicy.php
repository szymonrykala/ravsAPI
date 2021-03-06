<?php

declare(strict_types=1);


namespace App\Domain\Reservation\Policy;

use App\Domain\Reservation\Policy\Exception\PassedReservationUpdateException;
use App\Domain\Reservation\Policy\Exception\TooLateReservationUpdateException;
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
        $this->reservationHasNotStarted();
        // $this->updateMaxTimeBefore();

        $timeUpdate = isset($form->plannedEnd) || isset($form->plannedStart);
        $placeUpdate = isset($form->roomId);



        if ($placeUpdate || $timeUpdate) {
            $this->room = isset($form->roomId) ? $this->roomRepository->byId($form->roomId)
                : $originalReservation->room;

            $this->building = $this->buildingRepository->byId($this->room->buildingId);

            if ($placeUpdate) {
                $this->roomBelongsToBuilding();
                $this->roomCannotBeBlocked();
            }

            $this->start = $form->plannedStart ?? $originalReservation->plannedStart;
            $this->end = $form->plannedEnd ?? $originalReservation->plannedEnd;

            if ($timeUpdate) {

                $this->reservationHasFutureTime();
                $this->reservationTimeSlotLengthIsOk();
                $this->reservationWhenBuildingIsOpen();
            }
            $this->noCrossingReservationWasMade($originalReservation->id);
        }
    }

    /**
     * check if update is max 1 hour before - disabled
     */
    private function updateMaxTimeBefore(): void
    {
        /** @var DateInterval $timeDiff*/
        $timeDiff = $this->reservation->plannedStart->diff(new JsonDateTime('now'));

        if ($timeDiff->d === 0 && $timeDiff->h < 1) throw new TooLateReservationUpdateException();
    }

    /**
     * chceck if reservation has already started
     */
    private function reservationHasNotStarted(): void
    {
        if ($this->reservation->plannedStart < new JsonDateTime('now'))
            throw new PassedReservationUpdateException();
    }
}
