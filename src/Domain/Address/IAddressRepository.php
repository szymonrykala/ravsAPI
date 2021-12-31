<?php
declare(strict_types=1);

namespace App\Domain\Address;

use App\Domain\Model\IRepository;



interface IAddressRepository extends IRepository
{

    /**
     * @param Address $access
     */
    public function save(Address $address): void;

    /**
     * @param string $country
     * @param string $town
     * @param string $postalCode
     * @param string $street
     * @param string $number
     */
    public function create(
        string $country,
        string $town,
        string $postalCode,
        string $street,
        string $number
    ): int;
}