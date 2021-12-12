<?php

declare(strict_types=1);

namespace App\Domain\Address;

use App\Domain\Model\Model;
use App\Utils\JsonDateTime;


final class Address extends Model
{
    public function __construct(
        public int $id,
        public string $country,
        public string $town,
        public string $postalCode,
        public string $street,
        public string $number,
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
        $out = array_merge(
            [
                "country" => $this->country,
                "town" => $this->town,
                "postalCode" => $this->postalCode,
                "street" => $this->street,
                "number" => $this->number,
            ],
            parent::jsonSerialize()
        );

        return $out;
    }
}
