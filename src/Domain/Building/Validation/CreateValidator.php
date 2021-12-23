<?php

declare(strict_types=1);

namespace App\Domain\Building\Validation;



final class CreateValidator extends BuildingValidation
{
    /**
     * {@inheritDoc}
     */
    protected function defineSchema($validator): void
    {
        $this->setAsRequired($this->fields);

        parent::defineSchema($validator);
    }
}
