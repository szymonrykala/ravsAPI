<?php
declare(strict_types=1);

namespace App\Application\Actions\Address;

use Psr\Http\Message\ResponseInterface as Response;


class ListAllAddressesAction extends AddressAction
{
    /**
     * {@inheritdoc}
     */
    protected function action(): Response
    {
        $addresses = $this->addressRepository->all();

        $this->logger->info("All addresses has been viewed.");

        return $this->respondWithData($addresses);
    }
}
