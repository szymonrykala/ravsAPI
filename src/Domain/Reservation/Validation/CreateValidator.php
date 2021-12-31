<?php

declare(strict_types=1);

namespace App\Domain\Reservation\Validation;



final class CreateValidator extends ReservationValidation
{
    /**
     * {@inheritDoc}
     */
    protected function defineSchema($validator): void
    {
        $this->setAsRequired([
            'title',
            'description',
            'plannedStart',
            'plannedEnd'
        ]);

        parent::defineSchema($validator);
    }
}
