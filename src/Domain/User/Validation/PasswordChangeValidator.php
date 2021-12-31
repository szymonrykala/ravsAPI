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
        $this->setAsRequired($this->fields);

        $this->setUpPassword($validator, 'newPassword');

        parent::defineSchema($validator);
    }
}
