<?php

declare(strict_types=1);

namespace App\Domain\Access\Validation;



final class CreateValidator extends AccessValidation
{
    /**
     * {@inheritDoc}
     */
    protected function defineSchema($validator): void
    {
        $this->setAsRequired([
            'name' => 'Nazwa klasy dostępu jest wymagana',
        ]);

        parent::defineSchema($validator);
    }
}
