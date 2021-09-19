<?php

declare(strict_types=1);

namespace App\Infrastructure\Repository;

use App\Domain\Address\IAddressRepository;
use App\Infrastructure\Repository\BaseRepository;

use App\Domain\Building\{
    IBuildingRepository,
    Building
};

use App\Domain\Image\ImageRepositoryInterface;
use App\Domain\Model\Model;
use App\Infrastructure\Database\IDatabase;
use App\Utils\JsonDateTime;


final class BuildingRepository extends BaseRepository implements IBuildingRepository
{
    protected string $table = 'building';
    private ImageRepositoryInterface $imageRepository;
    private IAddressRepository $addressRepository;

    private bool $addressLoading = FALSE;


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
            ':openTime' =>  $building->openTime->getTime(),
            ':closeTime' => $building->closeTime->getTime(),
            ':id' => $building->id
        ];

        $this->db->query($sql, $params);
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
        $sql = "INSERT `$this->table`(name, address, open_time, close_time, image) 
                    VALUES(:name, :address, :openTime, :closeTime, (SELECT `value` FROM configuration WHERE `key`='BUILDING_IMAGE'))";
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
