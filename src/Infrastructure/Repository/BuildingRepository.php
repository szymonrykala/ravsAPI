<?php

declare(strict_types=1);

namespace App\Infrastructure\Repository;

use App\Domain\Address\IAddressRepository;
use App\Infrastructure\Repository\BaseRepository;

use App\Domain\Building\{
    IBuildingRepository,
    Building
};

use App\Domain\Exception\DomainResourceNotFoundException;
use App\Domain\Image\ImageRepositoryInterface;
use App\Infrastructure\Database\IDatabase;
use DateTime;



final class BuildingRepository extends BaseRepository implements IBuildingRepository
{
    protected string $table = 'building';
    private ImageRepositoryInterface $imageRepository;
    private IAddressRepository $addressRepository;

    private bool $addressLoading = FALSE;

    /**
     * @param IDatabase db database
     * @param ImageInterfaceRepository imagerRpository
     */
    public function __construct(
        IDatabase $db,
        ImageRepositoryInterface $imageRepository,
        IAddressRepository $addressRepository
    ) {
        parent::__construct($db);
        $this->imageRepository = $imageRepository;
        $this->addressRepository = $addressRepository;
    }

    /**
     * {@inheritdoc}
     */
    public function withAddress(): IBuildingRepository
    {
        $this->addressLoading = TRUE;
        return $this;
    }

    /**
     * @param array $data from database
     * @return Building
     */
    protected function newItem(array $data): Building
    {
        $image = $this->imageRepository->byId((int)$data['image']);
        $address = $this->addressLoading ? $this->addressRepository->byId((int) $data['address']): NULL;

        return new Building(
            (int)   $data['id'],
                    $data['name'],
                    $image,
                   $address,
            new DateTime($data['open_time']),
            new DateTime($data['close_time']),
            new DateTime($data['created']),
            new DateTime($data['updated']),
            (int) $data['image'],
            (int) $data['address']
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
