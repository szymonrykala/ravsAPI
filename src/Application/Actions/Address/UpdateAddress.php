<?php
declare(strict_types=1);

namespace App\Application\Actions\Address;

use Psr\Http\Message\ResponseInterface as Response;

use App\Domain\Address\Address;
use App\Domain\Address\Validation\UpdateValidator;


class UpdateAddress extends AddressAction
{
    /**
     * {@inheritdoc}
     */
    protected function action(): Response
    {
        $form = $this->getFormData();

        $validator = new UpdateValidator();
        $validator->validateForm($form);

        $id = (int) $this->resolveArg($this::ADDRESS_ID);

        /** @var Address $address */
        $address = $this->addressRepository->byId($id);

        $address->update($form);

        $this->addressRepository->save($address);
        
        $this->logger->info("Address id {$id} was updated.");

        return $this->respondWithData();
    }
}
