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
        $this->setAsRequired([
            'email' => 'email jest wymagany',
            'password' => 'hasło jest wymagane',
            'code' => 'kod jest wymagany'
        ]);

        $this->setUpPassword($validator, 'password');

        parent::defineSchema($validator);
    }
}
