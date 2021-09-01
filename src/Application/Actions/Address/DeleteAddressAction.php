<?php
declare(strict_types=1);

namespace App\Application\Actions\Address;

use Psr\Http\Message\ResponseInterface as Response;


class DeleteAddressAction extends AddressAction
{
    /**
     * {@inheritdoc}
     */
    protected function action(): Response
    {
        $id = $this->resolveArg('address_id');

        $this->addressRepository->deleteById( (int) $id);

        $this->logger->info("Address id {$id} was deleted.");

        return $this->respondWithData();
    }
}
