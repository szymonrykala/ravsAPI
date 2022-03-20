<?php

declare(strict_types=1);

namespace App\Application\Actions\Access;

use App\Domain\Access\Validation\CreateValidator;
use Psr\Http\Message\ResponseInterface as Response;


class CreateAccess extends AccessAction
{
    /**
     * {@inheritdoc}
     */
    protected function action(): Response
    {
        $form = $this->getFormData();

        // data input validator
        $validator = new CreateValidator();
        $validator->validateForm($form);

        $accessId = $this->accessRepository->create(
            $form->name,
            $form->owner ?? FALSE,
            $form->accessAdmin ?? FALSE,
            $form->premisesAdmin ?? FALSE,
            $form->keysAdmin ?? FALSE,
            $form->reservationsAdmin ?? FALSE,
            $form->reservationsAbility ?? FALSE,
            $form->logsAdmin ?? FALSE,
            $form->statsViewer ?? FALSE
        );

        $this->logger->info("Access id=${accessId} has been created.");

        return $this->respondWithData($accessId, 201);
    }
}
