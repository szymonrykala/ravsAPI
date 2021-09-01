<?php
declare(strict_types=1);

namespace App\Application\Actions\Address;

use App\Application\Actions\Action;
use App\Domain\Address\IAddressRepository;
use Psr\Log\LoggerInterface;
use App\Domain\Request\RequestRepositoryInterface;

abstract class AddressAction extends Action
{
    protected IAddressRepository $addressRepository;

    /**
     * @param LoggerInterface $logger
     * @param IAddressRepository $addressRepository
     */
    public function __construct(
        LoggerInterface $logger,
        IAddressRepository $addressRepository,
        RequestRepositoryInterface $requestRepo
    ) {
        parent::__construct($logger, $requestRepo);
        $this->addressRepository = $addressRepository;
    }
}

