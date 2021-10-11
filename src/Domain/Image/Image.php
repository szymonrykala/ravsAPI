<?php

declare(strict_types=1);

namespace App\Domain\Image;

use App\Domain\Model\Model;
use App\Utils\JsonDateTime;

class Image extends Model
{

    public string $name;
    public int $size;

    public function __construct(
        int $id,
        string $name,
        int $size,
        JsonDateTime $created,
        JsonDateTime $updated
    ) {
        parent::__construct($id, $created, $updated);

        $this->name = $name;
        $this->size = $size;
    }

    /**
     * {@inheritDoc}
     */
    public function jsonSerialize(): array
    {
        return array_merge(
            [
                'size' => $this->size
            ],
            parent::jsonSerialize()
        );
    }
}
