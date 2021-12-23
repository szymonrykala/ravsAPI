<?php

declare(strict_types=1);

namespace App\Domain\User\Validation;

use Cake\Validation\Validator;



class CreateValidator extends UserValidator
{
    /**
     * {@inheritDoc}
     */
    protected array $fields = [
        'email',
        'password',
        'name',
        'surname'
    ];


    /**
     * {@inheritDoc}
     */
    protected function defineSchema(Validator $validator): void
    {
        $this->setAsRequired($this->fields);

        $this->setAsNameString('name', 'Imię ma niepoprawny format');
        $this->setAsNameString('surname', 'Nazwisko ma niepoprawny format');

        $this->setUpPassword($validator, 'password');

        parent::defineSchema($validator);
    }
}
