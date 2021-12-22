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
        $this->setAsNameString('name', 'Nazwa budynku zawiera niedozwolone znaki');
        $this->setAsType(['addressId'], 'integer');

        $validator
            ->time('openTime', 'Niepoprawny format godziny otwarcia')
            ->time('closeTime', 'Niepoprawny format godziny zamknięcia');
    }
}
