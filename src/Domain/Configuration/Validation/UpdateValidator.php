<?php

declare(strict_types=1);

namespace App\Domain\Configuration\Validation;

use App\Domain\Model\SchemaValidator;
use Cake\Validation\Validator;


class UpdateValidator extends SchemaValidator
{
    /**
     * {@inheritDoc}
     */
    protected array $fields = [
        'maxImageSize',
        'defaultUserAccess',
        'maxReservationTime',
        'minReservationTime',
        'reservationHistory',
        'requestHistory'
    ];

    /**
     * {@inheritDoc}
     */
    protected function defineSchema(Validator $validator): void
    {
        $this->setAsType($this->fields, 'integer');

        $validator->range('maxImageSize', [10, 80_000_000], 'Wielkość obrazu musi mieścić się w przedziale 10b - 10MB');

        foreach ([
            'defaultUserAccess',
            'maxReservationTime',
            'minReservationTime',
            'reservationHistory',
            'requestHistory'
        ] as $field) {
            $validator->greaterThan($field, 0, 'Wartość musi być większa niż \'0\'');
        }
    }
}
