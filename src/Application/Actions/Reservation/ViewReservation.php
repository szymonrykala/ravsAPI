<?php
declare(strict_types=1);

namespace App\Application\Actions\Reservation;


use Psr\Http\Message\ResponseInterface as Response;



class ViewReservation extends ReservationAction
{
    /**
     * {@inheritdoc}
     */
    protected function action():Response
    {
        $reservationId = (int) $this->resolveArg('reservation_id');

        $item = $this->reservations->byId($reservationId);

        $this->logger->info("reservation with id ${reservationId} was viewed ");    
    
        return $this->respondWithData($item);
    }
}