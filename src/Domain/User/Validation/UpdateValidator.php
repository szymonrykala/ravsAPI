<?php

declare(strict_types=1);

namespace App\Domain\User\Validation;

use Cake\Validation\Validator;



class UpdateValidator extends UserValidator
{
    /**
     * {@inheritDoc}
     */
    protected array $fields = [
        'name',
        'surname',
        'metadata'
    ];


    /**
     * {@inheritDoc}
     */
    protected function defineSchema(Validator $validator): void
    {
        $this->setAsNameString('name', 'Imię ma niepoprawny format');
        $this->setAsNameString('surname', 'Nazwisko ma niepoprawny format');

        $validator->addNested('metadata', $validator);

        parent::defineSchema($validator);
    }
}
