<?php

declare(strict_types=1);

namespace App\Domain\User\Validation;

use App\Domain\Model\SchemaValidator;
use Cake\Validation\Validator;



class UserValidator extends SchemaValidator
{
    /**
     * {@inheritDoc}
     */
    protected function defineSchema(Validator $validator): void
    {

        $this->setAsType([
            'code', 'email', 'password', 'newPassword', 'name', 'surname'
        ], 'string');

        $validator
            ->email('email', false, 'Podany email ma zły format')

            ->regex('code', '/^\w+$/', 'Podany kod ma zły format')
            ->maxLength('code', 20, 'Podany kod jest nieprawidłowy');
    }

    /**
     * sets up password validation for given key
     */
    protected function setUpPassword(Validator $validator, string $key = 'password'): void
    {
        $validator
            ->minLength($key, 8, 'Hasło jest zby krótkie')
            ->maxLength($key, 32, 'Hasło jest zbyt długie')
            ->regex(
                $key,
                '/^(?=.*[0-9])(?=.*[a-z])(?=.*[A-Z])(?=.*[!@#$%^&]).{8,32}$/',
                'Hasło ma zły format, zawrzyj minimum 1 cyfra, wielka literę i znak specjalny(!@#$%^&)'
            );
    }
}
