<?php

declare(strict_types=1);

namespace App\Domain\Address;

use App\Domain\Model\Model;

use App\Utils\JsonDateTime;

class Address extends Model
{
    public string $country;
    public string $town;
    public string $postalCode;
    public string $street;
    public string $number;


    /**
     * @param int    $id
     * @param string $country
     * @param string $town
     * @param string $postalCode
     * @param string $street
     * @param string $number
     * @param string $created
     * @param string $updated
     */
    public function __construct(
        int $id,
        string $country,
        string $town,
        string $postalCode,
        string $street,
        string $number,
        JsonDateTime $created,
        JsonDateTime $updated
    ) {
        parent::__construct($id, $created, $updated);

        $this->country = $country;
        $this->town = $town;
        $this->postalCode = $postalCode;
        $this->street = $street;
        $this->number = $number;
    }

    /**
     * @return array
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
