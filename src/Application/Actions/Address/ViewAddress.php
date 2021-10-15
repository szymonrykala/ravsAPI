<?php
declare(strict_types=1);

namespace App\Application\Actions\Address;

use Psr\Http\Message\ResponseInterface as Response;


class ViewAddress extends AddressAction
{
    /**
     * {@inheritdoc}
     */
    protected function action(): Response
    {
        $id = $this->resolveArg($this::ADDRESS_ID);
        $address = $this->addressRepository->byId( (int) $id);

        $this->logger->info("Address id {$id} has been viewed.");

        return $this->respondWithData($address);
    }
}
