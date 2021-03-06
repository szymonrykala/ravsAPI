<?php

declare(strict_types=1);

namespace App\Application\Actions\Address;

use Psr\Http\Message\ResponseInterface as Response;
use App\Domain\Address\Validation\CreateValidator;


class CreateAddress extends AddressAction
{
    /**
     * {@inheritdoc}
     */
    protected function action(): Response
    {
        $form = $this->getFormData();

        $validator = new CreateValidator();
        $validator->validateForm($form);

        $id = $this->addressRepository->create(
            $form->country,
            $form->town,
            $form->postalCode,
            $form->street,
            $form->number
        );


        $this->logger->info("Address id {$id} was created.");

        return $this->respondWithData($id, 201);
    }
}
