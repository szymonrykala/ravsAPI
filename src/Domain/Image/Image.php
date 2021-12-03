<?php

declare(strict_types=1);

namespace App\Domain\Image;

use App\Domain\Model\Model;
use App\Utils\JsonDateTime;

final class Image extends Model
{
    public function __construct(
        public int $id,
        public string $publicId,
        public int $size,
        public ?string $url,
        public JsonDateTime $created,
        public JsonDateTime $updated
    ) {
        parent::__construct($id, $created, $updated);
    }

    /**
     * {@inheritDoc}
     */
    public function jsonSerialize(): array
    {
        return array_merge(
            [
                'size' => $this->size,
                'url' => $this->url ?? ('/images/' . $this->publicId)
            ],
            parent::jsonSerialize()
        );
    }
}
