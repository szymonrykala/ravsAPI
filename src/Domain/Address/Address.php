<?php

declare(strict_types=1);

namespace App\Domain\Address;

use App\Domain\Model\Model;

use DateTime;

class Address extends Model
{
    public string $country;
    public string $town;
    public string $postalCode;
    public string $street;
    public string $number;

    private array $buildings = [];
    private bool $buildingsWasSet = FALSE;

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
        DateTime $created,
        DateTime $updated
    ) {
        parent::__construct($id, $created, $updated);

        $this->country = $country;
        $this->town = $town;
        $this->postalCode = $postalCode;
        $this->street = $street;
        $this->number = $number;
    }

    /**
     * @param Building[]
     * @return void
     */
    public function setBuildings(array $buildings): void
    {
        $this->buildings = $buildings;
        $this->buildingsWasSet = TRUE;
    }

    /**
     * @return array
     */
    public function jsonSerialize(): array
    {
        $out = [
            "id" => $this->id,
            "country" => $this->country,
            "town" => $this->town,
            "postalCode" => $this->postalCode,
            "street" => $this->street,
            "number" => $this->number,
            "created" => $this->created->format('c'),
            "updated" => $this->updated->format('c')
        ];
        if ($this->buildingsWasSet) $out['buildings'] = $this->buildings;
        
        return $out;
    }
}
