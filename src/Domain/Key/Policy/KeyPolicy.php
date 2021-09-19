<?php

declare(strict_types=1);

namespace App\Domain\Key\Policy;

use App\Domain\Reservation\Reservation;
use App\Domain\Room\Room;



class KeyPolicy
{
    protected Reservation $resevation;
    protected Room $room;

    /**
     * @param Reservation resevation
     * @param Room room
     */
    public function __construct(Reservation &$reservation)
    {
        $this->resevation = $reservation;
        $this->room = $reservation->room;
    }
}
