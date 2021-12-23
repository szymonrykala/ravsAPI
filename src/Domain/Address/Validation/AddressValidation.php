<?php

declare(strict_types=1);

namespace App\Domain\Address\Validation;

use App\Domain\Model\SchemaValidator;



class AddressValidation extends SchemaValidator
{
    /**
     * {@inheritDoc}
     */
    protected array $fields = [
        'country',
        'town',
        'street',
        'postalCode',
        'number'
    ];

    /**
     * {@inheritDoc}
     */
    protected function defineSchema($validator): void
    {
        $this->setAsType($this->fields, 'string');

        $validator
            ->minLength('country', 3, 'Nazwa kraju jest zbyt krótka')
            ->maxLength('country', 60, 'Nazwa kraju jest zbyt długa')

            ->minLength('town', 3, 'Nazwa miasta jest zbyt krótka')
            ->maxLength('town', 60, 'Nazwa miasta jest zbyt długa')

            ->regex(
                'postalCode',
                '/^\\d{2}-\\d{3}$/',
                'Kod pocztowy ma niepoprawny format'
            )

            ->minLength('street', 3, 'Nazwa ulicy jest zbyt krótka')
            ->maxLength('street', 60, 'Nazwa ulicy jest zbyt długa')

            ->regex(
                'number',
                '/^\\d+[A-z]?(\/\\d+[A-z]?)?$/',
                'Numer budynku ma niepoprawny format'
            );
    }
}
