<?php
declare(strict_types=1);

namespace App\Application\Actions;

use JsonSerializable;

class ActionError implements JsonSerializable
{
    public const BAD_REQUEST = 'BAD_REQUEST';
    public const INSUFFICIENT_PRIVILEGES = 'INSUFFICIENT_PRIVILEGES';
    public const NOT_ALLOWED = 'NOT_ALLOWED';
    public const NOT_IMPLEMENTED = 'NOT_IMPLEMENTED';
    public const RESOURCE_NOT_FOUND = 'RESOURCE_NOT_FOUND';
    public const SERVER_ERROR = 'SERVER_ERROR';
    public const UNAUTHENTICATED = 'UNAUTHENTICATED';
    public const VALIDATION_ERROR = 'VALIDATION_ERROR';
    public const VERIFICATION_ERROR = 'VERIFICATION_ERROR';
    public const CONFLICT = 'CONFLICT';
    public const UNPROCESSABLE_ENTITY = 'UNPROCESSABLE_ENTITY';

    private string $type;
    private string $description;


    public function __construct(string $type, ?string $description)
    {
        $this->type = $type;
        $this->description = $description;
    }

    /**
     * get type of the error
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * set type of the error
     */
    public function setType(string $type): ActionError
    {
        $this->type = $type;
        return $this;
    }

    /**
     * get description of the error
     */
    public function getDescription(): string
    {
        return $this->description;
    }

    /**
     * set description of the error
     */
    public function setDescription(?string $description = null): ActionError
    {
        $this->description = $description;
        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function jsonSerialize()
    {
        $payload = [
            'type' => $this->type,
            'description' => $this->description,
        ];

        return $payload;
    }
}
