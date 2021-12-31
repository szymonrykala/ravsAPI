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
            'name',
            'roomType',
            'seatsCount',
            'floor'
        ]);

        parent::defineSchema($validator);
    }
}
