<?php

declare(strict_types=1);

namespace App\Domain\Access\Validation;

use App\Domain\Model\SchemaValidator;



class AccessValidation extends SchemaValidator
{
    /**
     * {@inheritDoc}
     */
    protected array $fields = [
        'name',
        'owner',
        'accessAdmin',
        'premisesAdmin',
        'keysAdmin',
        'reservationsAdmin',
        'reservationsAbility',
        'logsAdmin',
        'statsViewer'
    ];

    /**
     * {@inheritDoc}
     */
    protected function defineSchema($validator): void
    {
        $this->setAsType(['name'], 'string');
        $this->setAsType([
            'owner',
            'accessAdmin',
            'premisesAdmin',
            'keysAdmin',
            'reservationsAdmin',
            'reservationsAbility',
            'logsAdmin',
            'statsViewer',
        ], 'boolean');

        $this->setAsNameString('name', 'Nazwa klasy dostępu niedozwolone znaki');
    }
}
