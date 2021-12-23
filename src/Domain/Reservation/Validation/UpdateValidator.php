<?php

declare(strict_types=1);

namespace App\Domain\Reservation\Validation;


final class UpdateValidator extends ReservationValidation
{

    /**
     * {@inheritDoc}
     */
    protected function defineSchema($validator): void
    {
        parent::defineSchema($validator);
    }
}
