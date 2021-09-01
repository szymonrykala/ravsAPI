<?php

declare(strict_types=1);

namespace App\Infrastructure\Repository;

use App\Infrastructure\Repository\BaseRepository;

use App\Domain\Building\{
    IBuildingRepository,
    Building
};

use App\Domain\Exception\DomainResourceNotFoundException;

use DateTime;



class BuildingRepository extends BaseRepository implements IBuildingRepository
{
    protected string $table = 'building';

    /**
     * @param array $data from database
     * @return Building
     */
    protected function newItem(array $data): Building
    {
        return new Building(
            (int)   $data['id'],
                    $data['name'],
            (int)   $data['image'],
            (int)   $data['address'],
            new DateTime($data['open_time']),
            new DateTime($data['close_time']),
            new DateTime($data['created']),
            new DateTime($data['updated']),
        );
    }

    /**
     * {@inheritdoc}
     */
    public function save(Building $building): void
    {
        $building->validate();
        $sql = "UPDATE `$this->table` SET
                    `name` = :name,
                    `address` = :addressId,
                    `image` = :imageId,
                    `close_time` = :closeTime,
                    `open_time` = :openTime
                WHERE `id` = :id";

        $params = [
            ':name' => $building->name,
            ':addressId' => $building->addressId,
            ':imageId' => $building->imageId,
            ':openTime' =>  $building->openTime->format('H:i:s'),
            ':closeTime' => $building->closeTime->format('H:i:s'),
            ':id' => $building->id
        ];

        $this->db->query($sql, $params);
    }

    /**
     * {@inheritdoc}
     */
    public function create(
        string $name,
        DateTime $openTime,
        DateTime $closeTime,
        int $addressId
    ): int {
        $sql = "INSERT `$this->table`(name, address, open_time, close_time) 
                    VALUES(:name, :address, :openTime, :closeTime)";
        $params = [
            ':name' => $name,
            ':address' => $addressId,
            ':openTime' => $openTime->format('H:i:s'),
            ':closeTime' => $closeTime->format('H:i:s'),
        ];

        $this->db->query($sql, $params);
        return $this->db->lastInsertId();
    }

    /**
     * {@inheritdoc}
     */
    public function byIdAndAddressId(int $buildingId, int $addressId): Building
    {
        $sql = "SELECT * FROM `$this->table` WHERE `id` = :id AND `address` = :addressId";
        $params = [
            ':id' => $buildingId,
            ':addressId' => $addressId
        ];

        $result = $this->db->query($sql, $params);
        $buildingData = array_pop($result);

        if (empty($buildingData)) {
            throw new DomainResourceNotFoundException();
        }

        return $this->newItem($buildingData);
    }

    /**
     * {@inheritdoc}
     */
    public function deleteById(int $id): void
    {
        $sql = "DELETE FROM `$this->table` WHERE `id` = :id";
        $params = [':id' => $id];
        $this->db->query($sql, $params);
    }
}
