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
        $this->setAsRequired([
            'country' => 'Nazwa Kraju jest wymagana',
            'town' => 'Nazwa miasta jest wymagana',
            'postalCode' => 'Kod pocztowy jest wymagany',
            'street' => 'Nazwa ulicy jest wymagana',
            'number' => 'Numer budynku jest wymagany'
        ]);

        parent::defineSchema($validator);
    }
}
