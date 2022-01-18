<?php

declare(strict_types=1);

namespace App\Infrastructure\Repository;

use App\Domain\Address\IAddressRepository;
use App\Infrastructure\Repository\BaseRepository;

use App\Domain\Building\{
    IBuildingRepository,
    Building
};

use App\Domain\Image\IImageRepository;
use App\Domain\Model\Model;
use App\Utils\JsonDateTime;
use Psr\Container\ContainerInterface;



final class BuildingRepository extends BaseRepository implements IBuildingRepository
{
    protected string $table = '`building`';
    private bool $addressLoading = FALSE;

    public function __construct(
        ContainerInterface $di,
        private IImageRepository $imageRepository,
        private IAddressRepository $addressRepository
    ) {
        parent::__construct($di);
    }

    /**
     * {@inheritDoc}
     */
    public function withAddress(): IBuildingRepository
    {
        $this->addressLoading = TRUE;
        return $this;
    }

    /**
     * {@inheritDoc}
     */
    protected function newItem(array $data): Building
    {
        $image = $this->imageRepository->byId((int)$data['image']);
        $address = $this->addressLoading ? $this->addressRepository->byId((int) $data['address']) : NULL;

        return new Building(
            (int)   $data['id'],
            $data['name'],
            $image,
            $address,
            new JsonDateTime($data['open_time']),
            new JsonDateTime($data['close_time']),
            new JsonDateTime($data['created']),
            new JsonDateTime($data['updated']),
            (int) $data['image'],
            (int) $data['address']
        );
    }

    /**
     * {@inheritDoc}
     */
    public function save(Building $building): void
    {
        $building->validate();
        $sql = "UPDATE $this->table SET
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
            ':openTime' =>  $building->openTime->getTime(),
            ':closeTime' => $building->closeTime->getTime(),
            ':id' => $building->id
        ];

        $this->db->query($sql, $params);
    }

    /**
     * {@inheritDoc}
     */
    public function setDefaultImage(Building $building): void
    {
        $this->db->query(
            "UPDATE $this->table SET `image` = DEFAULT WHERE `id` = :id",
            [':id' => $building->id]
        );
    }

    /**
     * {@inheritDoc}
     */
    public function create(
        string $name,
        JsonDateTime $openTime,
        JsonDateTime $closeTime,
        int $addressId
    ): int {
        $sql = "INSERT INTO $this->table(`name`, `address`, `open_time`, `close_time`, `image`) 
                    VALUES(:name, :address, :openTime, :closeTime, DEFAULT)";
        $params = [
            ':name' => $name,
            ':address' => $addressId,
            ':openTime' => $openTime->getTime(),
            ':closeTime' => $closeTime->getTime(),
        ];

        $this->db->query($sql, $params);
        return $this->db->lastInsertId();
    }

    /**
     * {@inheritDoc}
     * @param Building
     */
    public function delete(Model $building): void
    {
        parent::delete($building);
        $this->imageRepository->delete($building->image);
    }
}
