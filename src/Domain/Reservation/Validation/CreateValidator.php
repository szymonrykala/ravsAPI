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
            'title' => 'Tytuł rezerwacji jest wymagany',
            'description' => 'Opis rezerwacji jest wymagany',
            'plannedStart' => 'Data początku rezerwacji wymagana',
            'plannedEnd' => 'Data końca rezerwacji jest wymagana'
        ]);

        parent::defineSchema($validator);
    }
}
