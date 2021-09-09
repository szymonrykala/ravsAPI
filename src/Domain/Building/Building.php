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
    public ?Address $address;

    public DateTime $openTime;    
    public DateTime $closeTime;

    public int $imageId;
    public int $addressId;
    

    /**
     * @param int    id
     * @param string name
     * @param Image  image
     * @param Address|NULL address
     * @param string created
     * @param string updated
     * @param int imageId
     * @param int addressId
     */
    public function __construct(
        int $id,
        string $name,
        Image $image,
        ?Address $address,
        DateTime $openTime,
        DateTime $closeTime,
        DateTime $created,
        DateTime $updated,
        int $imageId,
        int $addressId
    ){
        parent::__construct($id, $created, $updated);

        $this->name = $name;
        $this->image = $image;
        $this->address = $address;
        $this->closeTime = $closeTime;
        $this->openTime = $openTime;

        $this->addressId = $addressId;
        $this->imageId = $imageId;
    }


    /**
     * @return array
     */
    public function jsonSerialize():array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'image' => $this->image,
            'address' => $this->address ?? $this->addressId,
            'openTime' => $this->openTime->format('H:i:s'),
            'closeTime' => $this->closeTime->format('H:i:s'),
            'created' => $this->created->format('c'),
            'updated' => $this->updated->format('c')
        ];
    }
}