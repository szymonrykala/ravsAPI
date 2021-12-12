<?php

declare(strict_types=1);

namespace App\Domain\Building;

use App\Domain\Model\Model;
use App\Domain\Image\Image;
use App\Domain\Address\Address;
use App\Utils\JsonDateTime;


final class Building extends Model
{
    public function __construct(
        public int $id,
        public string $name,
        public Image $image,
        public ?Address $address,
        public JsonDateTime $openTime,
        public JsonDateTime $closeTime,
        public JsonDateTime $created,
        public JsonDateTime $updated,
        public int $imageId,
        public int $addressId
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
                'name' => $this->name,
                'image' => $this->image,
                'address' => $this->address ?? $this->addressId,
                'openTime' => $this->openTime->getTime(),
                'closeTime' => $this->closeTime->getTime(),
            ],
            parent::jsonSerialize()
        );
    }
}
