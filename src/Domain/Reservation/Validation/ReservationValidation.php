<?php

declare(strict_types=1);

namespace App\Domain\Reservation\Validation;

use App\Domain\Model\SchemaValidator;



class ReservationValidation extends SchemaValidator
{
    /**
     * {@inheritDoc}
     */
    protected array $fields = [
        'title', 'description', 'plannedStart', 'plannedEnd', 'roomId'
    ];

    /**
     * {@inheritDoc}
     */
    protected function defineSchema($validator): void
    {
        $this->setAsType([
            'title',
            'description',
            'plannedStart',
            'plannedEnd'
        ], 'string');

        $this->setAsType(['roomId'], 'integer');

        $this->setAsNameString('title', 'Tytuł rezerwacji zawiera niedozwolone znaki');
        $validator->maxLength('title', 120, 'Tytuł może mieć maksymalnie 120 znaków');

        $this->setAsNameString('description', 'Opis rezerwacji zawiera niedozwolone znaki');
        $validator->maxLength('description', 600, 'Opis rezerwacji może mieć maksymalnie 600 znaków');

        $validator
            ->dateTime('plannedStart', ['ymd'], 'Data startu rezerwacji ma zły format')
            ->dateTime('plannedEnd', ['ymd'], 'Data końca rezerwacji ma zły format');
    }
}
