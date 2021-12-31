<?php

declare(strict_types=1);

namespace App\Domain\Room\Validation;


final class UpdateValidator extends RoomValidation
{

    /**
     * {@inheritDoc}
     */
    protected function defineSchema($validator): void
    {
        array_push($this->fields, 'buildingId', 'blocked');

        parent::defineSchema($validator);
    }
}
