<?php

declare(strict_types=1);

namespace App\Application\Actions\Key;

use App\Domain\Exception\HttpConflictException;
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
        $reservationId = (int) $this->resolveArg('reservation_id');

        $form = $this->getFormData();

        /** @var Reservation */
        $reservation = $this->reservationRepository->byId($reservationId);

        $policy = NULL;

        if ($reservation->notStarted()) {
            // start reservation - pick up the key
            $policy = new IssueKeyPolicy($reservation);

        } elseif ($reservation->hasStarted()) {
            // end reservation - return the key
            $policy = new KeyReturnPolicy($reservation);

        } elseif ($reservation->hasEnded()) {
            throw new HttpConflictException($this->request, 'Reservation has already ended.');
        }

        $reservation = $policy($form->NFCTag);

        $this->roomRepository->save($reservation->room);
        $this->reservationRepository->save($reservation);

        return $this->respondWithData();
    }
}
