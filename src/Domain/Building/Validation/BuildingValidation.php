<?php

declare(strict_types=1);

namespace App\Domain\Building\Validation;

use App\Domain\Model\SchemaValidator;



class BuildingValidation extends SchemaValidator
{
    /**
     * {@inheritDoc}
     */
    protected array $fields = [
        'name', 'openTime', 'closeTime'
    ];

    /**
     * {@inheritDoc}
     */
    protected function defineSchema($validator): void
    {
        $this->setAsType($this->fields, 'string');
        $this->setAsType(['addressId'], 'integer');

        $this->setAsNameString('name', 'Nazwa budynku zawiera niedozwolone znaki');

        $validator
            ->time('openTime', 'Niepoprawny format godziny otwarcia')
            ->time('closeTime', 'Niepoprawny format godziny zamknięcia');
    }
}
