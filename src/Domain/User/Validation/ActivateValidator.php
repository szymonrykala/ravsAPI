<?php

declare(strict_types=1);

namespace App\Domain\User\Validation;

use Cake\Validation\Validator;



class ActivateValidator extends UserValidator
{
    /**
     * {@inheritDoc}
     */
    protected array $fields = [
        'email',
        'code',
        'password'
    ];


    /**
     * {@inheritDoc}
     */
    protected function defineSchema(Validator $validator): void
    {
        $this->setAsRequired($this->fields);

        $this->setUpPassword($validator, 'password');

        parent::defineSchema($validator);
    }
}
