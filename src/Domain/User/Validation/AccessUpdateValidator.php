<?php

declare(strict_types=1);

namespace App\Domain\User\Validation;

use Cake\Validation\Validator;



class AccessUpdateValidator extends UserValidator
{

    /**
     * {@inheritDoc}
     */
    protected array $fields = [
        'accessId'
    ];

    /**
     * {@inheritDoc}
     */
    protected function defineSchema(Validator $validator): void
    {
        $this->setAsRequired($this->fields);
        $this->setAsType(['accessId'], 'integer');

        parent::defineSchema($validator);
    }
}
