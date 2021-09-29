<?php

declare(strict_types=1);

namespace App\Domain\Image;

use App\Domain\Model\Model;
use App\Utils\JsonDateTime;

class Image extends Model
{

    public string $path;
    public int $size;

    public function __construct(
        int $id,
        string $path,
        int $size,
        JsonDateTime $created,
        JsonDateTime $updated
    ) {
        parent::__construct($id, $created, $updated);

        $this->path = $path;
        $this->size = $size;
    }

    /**
     * @return array
     */
    public function jsonSerialize(): array
    {
        return array_merge(
            [
                'path' => $this->path,
                'size' => $this->size
            ],
            parent::jsonSerialize()
        );
    }
}
