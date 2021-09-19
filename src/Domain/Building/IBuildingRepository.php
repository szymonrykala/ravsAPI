<?php
declare(strict_types=1);

namespace App\Domain\Building;

use App\Domain\Model\RepositoryInterface;
use App\Utils\JsonDateTime;


interface IBuildingRepository extends RepositoryInterface
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