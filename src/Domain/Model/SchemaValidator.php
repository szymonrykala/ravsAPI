<?php

declare(strict_types=1);

namespace App\Domain\Model;

use App\Domain\Exception\DomainBadRequestException;
use Cake\Validation\Validator;



abstract class SchemaValidator
{
    /** CakePHP validator  */
    private Validator $validator;

    /**
     * set of fields that form can contain
     */
    protected array $fields = [];


    public function __construct()
    {
        $this->validator = new Validator();
    }

    /**
     * Performs schmea definition on giver validator
     */
    abstract protected function defineSchema(Validator $validator): void;


    /**
     * Sets fields as required
     */
    protected function setAsRequired(array $fields): void
    {
        foreach ($fields as $field) {
            $this->validator->requirePresence($field, true, "Pole '$field' jest wymagane");
        }
    }


    /**
     * Declares given field as name string with regex: ^[\\w\\s\\.-]+$
     */
    protected function setAsNameString($field, $message)
    {
        $this->validator
            ->minLength($field, 1, 'Minimum znaków to 1')
            ->maxLength($field, 60, 'Maksymalna liczba znaków to 60')
            ->regex(
                $field,
                '/^[\w\s\.,-]+$/u',
                $message
            );
    }

    /** 
     * Declares a filed in list to be a specific type 
     */
    protected function setAsType(array $fields, string $type): void
    {
        foreach ($fields as $field) {
            $this->validator->add(
                $field,
                [
                    'is' . $type => [
                        'rule' => fn ($value) => gettype($value) === $type,
                        'message' => "Pole '$field' powinno być typu '$type'",
                    ]
                ]
            );
        }
    }


    /**
     * Executes validation rules
     * @throws DomainBadRequestException
     */
    public function validateForm(\stdClass $form): void
    {
        $formArr = (array) $form;

        $this->defineSchema($this->validator);

        foreach ($formArr as $field => $_) {
            if (!in_array($field, $this->fields))
                throw new DomainBadRequestException("Pole '$field' jest niedozwolone");
        }

        $errors = $this->validator->validate((array) $form);

        if (!empty($errors)) {
            $message = '';
            foreach ($errors as $field => $err) {
                $message .= implode(PHP_EOL, $err) . PHP_EOL;
            }

            throw new DomainBadRequestException($message);
        }
    }
}
