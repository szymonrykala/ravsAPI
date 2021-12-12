<?php

declare(strict_types=1);

namespace App\Domain\Model;

use App\Utils\JsonDateTime;
use stdClass;
use JsonSerializable;



abstract class Model implements JsonSerializable
{
    public function __construct(
        public int $id,
        public JsonDateTime $created,
        public JsonDateTime $updated
    ) {
    }

    /**
     * @throws ModelPropertyNotExistException
     */
    public function __set(string $name, $value)
    {
        throw new ModelPropertyNotExistException($name);
    }

    /**
     * Updates model properties with form data
     */
    public function update(stdClass $form): void
    {
        foreach ($form as $key => $value) $this->$key = $value;
    }

    /**
     * Domain object save validation callback.
     * Any rules checks on each update, can be implemented here.
     */
    public function validate(): void
    {
    }

    /**
     * Specify data which should be serialized to JSON
     */
    public function jsonSerialize(): array
    {
        return [
            'id' => $this->id,
            '_created' => $this->created,
            '_updated' => $this->updated
        ];
    }
}
