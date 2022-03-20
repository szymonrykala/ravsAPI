<?php
declare(strict_types=1);

namespace App\Application\Actions\Address;

use App\Application\Actions\Action;
use App\Domain\Address\IAddressRepository;
use Psr\Log\LoggerInterface;


abstract class AddressAction extends Action
{

    public function __construct(
        LoggerInterface $logger,
        protected IAddressRepository $addressRepository
    ) {
        parent::__construct($logger);
    }
}

