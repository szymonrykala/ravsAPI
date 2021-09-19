<?php

declare(strict_types=1);

namespace App\Domain\Key\Policy;

use App\Domain\Access\EndedReservationException;


class KeyReturnPolicy extends KeyPolicy
{

    public function __invoke(string $rfidKey)
    {
        $this->room->valiadateRfidKey($rfidKey);

        $this->resevation->end();

        $this->room->release();

        return $this->resevation;
    }
}
