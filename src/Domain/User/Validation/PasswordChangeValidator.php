<?php

declare(strict_types=1);

namespace App\Domain\User\Validation;

use Cake\Validation\Validator;



class PasswordChangeValidator extends UserValidator
{
    /**
     * {@inheritDoc}
     */
    protected array $fields = [
        'email',
        'code',
        'newPassword'
    ];


    /**
     * {@inheritDoc}
     */
    protected function defineSchema(Validator $validator): void
    {
        $this->setAsRequired([
            'email' => 'email jest wymagany',
            'newPassword' => 'hasło jest wymagane',
            'code' => 'kod jest wymagany'
        ]);

        $this->setUpPassword($validator, 'newPassword');

        parent::defineSchema($validator);
    }
}
