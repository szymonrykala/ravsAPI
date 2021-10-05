<?php

declare(strict_types=1);

namespace App\Application\Actions\Address;

use Psr\Http\Message\ResponseInterface as Response;


class DeleteAddress extends AddressAction
{
    /**
     * {@inheritdoc}
     */
    protected function action(): Response
    {
        $id = (int) $this->resolveArg('address_id');

        $address = $this->addressRepository->byId($id);
        $this->addressRepository->delete($address);

        $this->logger->info("Address id {$id} was deleted.");

        return $this->respondWithData();
    }
}
