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

            $reservation->start();
            $reservation->room->occupy();

            $message = "Rezerwacja rozpoczęta - wydaj klucz";
        } elseif ($reservation->hasStarted()) {
            $reservation->end();
            $reservation->room->release();

            $message = "Rezerwacja zakończona - odbierz klucz";
        } elseif ($reservation->hasEnded()) {
            throw new HttpConflictException($this->request, 'Rezerwacja już się zakończyła.');
        } else {
            throw new HttpInternalServerErrorException(
                $this->request,
                "Nie udało się przetworzyć żądania"
            );
        }


        $this->roomRepository->save($reservation->room);
        $this->reservationRepository->save($reservation);

        return $this->respondWithData($message);
    }

    /**
     * Checks if 2 hours passed, if yes - throws an error
     */
    private function checkIfAdditionalTimePassed(JsonDateTime $start)
    {
        $now = new JsonDateTime('now');

        if ($start->add(new \DateInterval('PT1H')) < $now) {
            throw new HttpConflictException(
                $this->request,
                "Dodatkowa godzina na odbiór rezerwacji już minęła."
            );
        }
    }
}
