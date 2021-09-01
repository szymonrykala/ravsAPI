<?php
declare(strict_types=1);

namespace App\Domain\Building;

use App\Domain\Model\Model;
use App\Domain\Image\Image;
use App\Domain\Address\Address;
use DateTime;


class Building extends Model
{
    public string $name;
    public Image $image;
    public Address $address;

    public DateTime $openTime;    
    public DateTime $closeTime;

    public int $imageId;
    public int $addressId;
    

    /**
     * @param int    id
     * @param string name
     * @param int    imageId
     * @param int    addressId
     * @param string created
     * @param string updated
     */
    public function __construct(
        int $id,
        string $name,
        int $imageId,
        int $addressId,
        DateTime $openTime,
        DateTime $closeTime,
        DateTime $created,
        DateTime $updated
    ){
        parent::__construct($id, $created, $updated);

        $this->name = $name;
        $this->imageId = $imageId;
        $this->addressId = $addressId;
        $this->closeTime = $closeTime;
        $this->openTime = $openTime;
    }


    /**
     * @return array
     */
    public function jsonSerialize():array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'image' => $this->image ?? $this->imageId,
            'address' => $this->address ?? $this->addressId,
            'openTime' => $this->openTime->format('H:i:s'),
            'closeTime' => $this->closeTime->format('H:i:s'),
            'created' => $this->created->format('c'),
            'updated' => $this->updated->format('c')
        ];
    }
}