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
     * @param int id
     */
    public function deleteById(int $id): void;
    
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

    /**
     * @param int buildingId
     * @param int addressId
     * @return Building
     * @throws DomainRecordNotFoundException
     */
    public function byIdAndAddressId(int $buildingId, int $addressId): Building;
}