<?php

declare(strict_types=1);

namespace App\Domain\Address\Validation;


final class UpdateValidator extends AddressValidation
{
    /**
     * {@inheritDoc}
     */
    protected function defineSchema($validator): void
    {
        parent::defineSchema($validator);
    }
}
