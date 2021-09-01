<?php
declare(strict_types=1);

namespace App\Infrastructure\Repository;
use App\Infrastructure\Repository\BaseRepository;

use App\Domain\Address\IAddressRepository;
use App\Domain\Address\Address;

use DateTime;


class AddressRepository extends BaseRepository implements IAddressRepository
{
    protected string $table = 'address';

    /**
     * @param array $data from database
     * @return Address
     */
    protected function newItem(array $data): Address
    {
        return new Address(
            (int)   $data['id'],
                    $data['country'],
                    $data['town'],
                    $data['postal_code'],
                    $data['street'],
                    $data['number'],
                    new DateTime($data['created']),
                    new DateTime($data['updated']),
        );
    }

    /**
     * {@inheritdoc}
     */
    public function deleteById(int $id): void
    {
        $this->byId($id);
        
        $sql = "DELETE FROM `$this->table` WHERE `id` = :id";
        $params = [':id' => $id];
        $this->db->query($sql, $params);
    }

    /**
     * {@inheritdoc}
     */
    public function save(Address $address): void
    {
        $address->validate();
        $sql = "UPDATE `$this->table` SET
                    `country` = :country,
                    `town` = :town,
                    `postal_code` = :postalCode,
                    `street` = :street,
                    `number` = :number
                WHERE `id` = :id";

        $params = [
            ':id' => $address->id,
            ':country' => ucfirst($address->country),
            ':town' => ucfirst($address->town),
            ':postalCode' => $address->postalCode,
            ':street' => ucfirst($address->street),
            ':number' => $address->number
        ];

        $this->db->query($sql, $params);
    }


    /**
     * {@inheritdoc}
     */
    public function create(
        string $country,
        string $town,
        string $postalCode,
        string $street,
        string $number
    ): int
    {
        $sql = "INSERT INTO `$this->table`
                    (`country`,`town`,`postal_code`,`street`,`number`)
                VALUES(:country, :town, :postalCode, :street, :number)";
        
        $params = [
            ':country' => ucfirst($country),
            ':town' => ucfirst($town),
            ':postalCode' => $postalCode,
            ':street' => ucfirst($street),
            ':number' => $number
        ];

        $this->db->query($sql, $params);

        return $this->db->lastInsertId();
    }
};
