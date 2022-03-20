<?php
declare(strict_types=1);

namespace App\Domain\Address;

use App\Domain\Model\IRepository;



interface IAddressRepository extends IRepository
{

    /**
     * saves address state
     */
    public function save(Address $address): void;

    /**
     * {@inheritDoc}
     */
    public function create(
        string $country,
        string $town,
        string $postalCode,
        string $street,
        string $number
    ): int;
}