<?php

declare(strict_types=1);

namespace App\Domain\Image;

use App\Domain\Model\Model;
use App\Utils\JsonDateTime;

class Image extends Model
{

    public string $path;

    /**
     * @param int       id,
     * @param string    path,
     * @param JsonDateTime    created,
     * @param JsonDateTime    updated
     */
    public function __construct(
        int $id,
        string $path,
        JsonDateTime $created,
        JsonDateTime $updated
    ) {
        parent::__construct($id, $created, $updated);

        $this->path = $path;
    }

    /**
     * @return array
     */
    public function jsonSerialize(): array
    {
        return array_merge(
            [
                "path" => $this->path,
            ],
            parent::jsonSerialize()
        );
    }
}
