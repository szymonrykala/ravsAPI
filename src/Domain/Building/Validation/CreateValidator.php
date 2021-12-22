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
        $this->setAsRequired([
            'name' => 'Nazwa budynku jest wymagana',
            'openTime' => 'Godzina otwarcia jest wymagany',
            'closeTime' => 'Godzina zamknięcia jest wymagany',
        ]);

        parent::defineSchema($validator);
    }
}
