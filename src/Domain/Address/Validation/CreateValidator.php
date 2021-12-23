<?php

declare(strict_types=1);

namespace App\Domain\Address\Validation;



final class CreateValidator extends AddressValidation
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
