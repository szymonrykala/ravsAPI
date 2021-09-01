<?php
declare(strict_types=1);

namespace App\Domain\Building;

use App\Domain\Model\RepositoryInterface;

use DateTime;

interface IBuildingRepository extends RepositoryInterface
{
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
        DateTime $openTime,
        DateTime $closeTime,
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