<?php

declare(strict_types=1);

namespace App\Domain\Key\Validation;

use App\Domain\Model\SchemaValidator;
use Cake\Validation\Validator;



class UpdateValidator extends SchemaValidator
{
    /**
     * {@inheritDoc}
     */
    protected array $fields = [
        'RFIDTag'
    ];

    /**
     * {@inheritDoc}
     */
    protected function defineSchema(Validator $validator): void
    {
        $this->setAsType($this->fields, 'string');
        $this->setAsRequired($this->fields);
    }
}
