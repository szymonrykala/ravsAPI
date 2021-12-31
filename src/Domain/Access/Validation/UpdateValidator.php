<?php

declare(strict_types=1);

namespace App\Domain\Access\Validation;


final class UpdateValidator extends AccessValidation
{

    /**
     * {@inheritDoc}
     */
    protected function defineSchema($validator): void
    {
        parent::defineSchema($validator);
    }
}
