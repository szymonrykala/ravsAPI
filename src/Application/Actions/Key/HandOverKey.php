<?php

declare(strict_types=1);

namespace App\Application\Actions\Key;

use App\Application\Exception\HttpConflictException;
use App\Domain\Key\Policy\IssueKeyPolicy;
use App\Domain\Key\Policy\KeyReturnPolicy;
use Psr\Http\Message\ResponseInterface as Response;
use App\Domain\Reservation\Reservation;


class HandOverKey extends KeyAction
{
    /**
     * {@inheritDoc}
     */
    protected function action(): Response
    {
        $reservationId = (int) $this->resolveArg($this::RESERVATION_ID);

        $form = $this->getFormData();

        /** @var Reservation */
        $reservation = $this->reservationRepository->byId($reservationId);

        $policy = NULL;
        $message = '';
        
        if ($reservation->notStarted()) {
            // start reservation - pick up the key
            $policy = new IssueKeyPolicy($reservation);
            $message = "Rezerwacja rozpoczęta - wydaj klucz";
        } elseif ($reservation->hasStarted()) {
            // end reservation - return the key
            $policy = new KeyReturnPolicy($reservation);
            $message = "Rezerwacja zakończona - odbierz klucz";
        } elseif ($reservation->hasEnded()) {
            throw new HttpConflictException($this->request, 'Rezerwacja już się zakończyła.');
        }

        $reservation = $policy($form->RFIDTag);

        $this->roomRepository->save($reservation->room);
        $this->reservationRepository->save($reservation);

        return $this->respondWithData($message);
    }
}
