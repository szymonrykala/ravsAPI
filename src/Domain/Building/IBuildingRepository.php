<?php
declare(strict_types=1);

namespace App\Domain\Building;

use App\Domain\Model\IRepository;
use App\Utils\JsonDateTime;


interface IBuildingRepository extends IRepository
{

    /**
     * enable address loading
     * @return IBuildingRepository
     */
    public function withAddress(): IBuildingRepository;
    
    /**
     * @param Building building
     */
    public function save(Building $building): void;

    /** sets default image for building */
    public function setDefaultImage(Building $building): void;

    /**
     * @param string name
     * @param int imageId
     * @param int addressId
     */
    public function create(
        string $name,
        JsonDateTime $openTime,
        JsonDateTime $closeTime,
        int $addressId
    ): int;

}