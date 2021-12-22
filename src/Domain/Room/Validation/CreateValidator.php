<?php

declare(strict_types=1);

namespace App\Domain\Room\Validation;



final class CreateValidator extends RoomValidation
{
    /**
     * {@inheritDoc}
     */
    protected function defineSchema($validator): void
    {
        $this->setAsRequired([
            'name' => 'Nazwa sali jest wymagana',
            'roomType' => 'Typ sali jest wymagany',
            'seatsCount' => 'Ilość miejsc jest wymagana',
            'floor' => 'Piętro na którym znajduje się sala jest wymagane'
        ]);

        parent::defineSchema($validator);
    }
}
