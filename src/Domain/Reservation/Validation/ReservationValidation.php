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

        $validator->regex('title', '/^[\w\s\.\,\'\"\(\)\!\?\-\<\>\;\:\/]+$/u', 'Tytuł rezerwacji zawiera niedozwolone znaki')
            ->maxLength('title', 120, 'Tytuł może mieć maksymalnie 120 znaków')

            ->regex('description',  '/^[\w\s\.\,\'\"\(\)\!\?\-\<\>\;\:\@]+$/u', "Opis rezerwacji zawiera niedozwolone znaki")
            ->maxLength('description', 600, 'Opis rezerwacji może mieć maksymalnie 600 znaków')

            ->dateTime('plannedStart', ['dmy'], 'Data startu rezerwacji ma zły format',)
            ->dateTime('plannedEnd', ['dmy'], 'Data końca rezerwacji ma zły format');
    }
}
