<?php

declare(strict_types=1);

namespace App\Utils;

use Opis\JsonSchema\{
    Validator
};


class JsonSchemaValidator extends Validator
{

    private const BASE_FOLDER = '../src/Domain/Schema';
    
    public const BASE_URI = 'api://schema/';

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * load all schemas
     * @return void 
     */
    public function loadDefaults(): void
    {
        $this->setMaxErrors(4);
        $this->resolver()
            ->registerPrefix($this::BASE_URI, $this::BASE_FOLDER);
    }
}
