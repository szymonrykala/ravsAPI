<?php

declare(strict_types=1);

namespace App\Domain\Building\Validation;


final class UpdateValidator extends BuildingValidation
{

    /**
     * {@inheritDoc}
     */
    protected function defineSchema($validator): void
    {
        array_push($this->fields, 'addressId');

        parent::defineSchema($validator);
    }
}
