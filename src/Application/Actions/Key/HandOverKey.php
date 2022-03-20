<?php

declare(strict_types=1);

namespace App\Application\Actions\Key;

use App\Application\Exception\HttpConflictException;
use Psr\Http\Message\ResponseInterface as Response;
use App\Domain\Reservation\Reservation;
use App\Utils\JsonDateTime;
use Slim\Exception\HttpInternalServerErrorException;


class HandOverKey extends KeyAction
{
    /**
     * {@inheritDoc}
     * @throws HttpConflictException
     * @throws HttpInternalServerErrorException
     */
    protected function action(): Response
    {
        $reservationId = (int) $this->resolveArg($this::RESERVATION_ID);

        $roomKey = $this->getFormData()->RFIDTag;


        /** @var Reservation */
        $reservation = $this->reservationRepository->byId($reservationId);

        $message = '';

        $reservation->room->valiadateRfidKey($roomKey);

        if ($reservation->notStarted()) {
            $this->checkIfAdditionalTimePassed($reservation->plannedStart);
            $this->checkIfNotTooEarly($reservation->plannedStart);

            $reservation->start();
            $reservation->room->occupy();

            $message = "Rezerwacja rozpoczęta - wydaj klucz";
            $this->logger->info("reservation id=${reservationId} started - excess the key");

        } elseif ($reservation->hasStarted()) {
            $this->checkIfNotTooEarly($reservation->plannedEnd);
            $reservation->end();
            $reservation->room->release();

            $message = "Rezerwacja zakończona - odbierz klucz";

            $this->logger->info("ending reservation id=${reservationId} - take the key");

        } elseif ($reservation->hasEnded()) {
            $this->logger->warning("reservation id=${reservationId} already passed");
            throw new HttpConflictException($this->request, 'Rezerwacja już się zakończyła.');

        } else {
            $this->logger->error("Could not recognize reservation id=${reservationId} status");
            throw new HttpInternalServerErrorException(
                $this->request,
                "Nie udało się rozpoznać statusu rezerwacji."
            );
        }


        $this->roomRepository->save($reservation->room);
        $this->reservationRepository->save($reservation);

        return $this->respondWithData($message);
    }


    /**
     * if given date is more than 1 hour before, throws exception
     * @throws HttpConflictException
     */
    private function checkIfNotTooEarly(JsonDateTime $plannedDate): void
    {
        $now = new JsonDateTime('now');

        // if now + 1h is still earlier than plannedDate
        if ($now->add(new \DateInterval('PT1H')) < $plannedDate) {
            throw new HttpConflictException(
                $this->request,
                "Rezerwację można odebrać lub oddać max 1 godzinę wcześniej"
            );
        }
    }


    /**
     * Checks if 2 hours passed, if yes - throws an error
     * @throws HttpConflictException
     */
    private function checkIfAdditionalTimePassed(JsonDateTime $start): void
    {
        $now = new JsonDateTime('now');
        $start = clone $start;

        if ($start->add(new \DateInterval('PT1H')) < $now) {
            throw new HttpConflictException(
                $this->request,
                "Dodatkowa godzina na odbiór rezerwacji już minęła."
            );
        }
    }
}
