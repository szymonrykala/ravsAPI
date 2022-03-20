<?php

declare(strict_types=1);

namespace App\Application\Actions\Access;

use App\Domain\Access\Validation\UpdateValidator;
use Psr\Http\Message\ResponseInterface as Response;



class UpdateAccess extends AccessAction
{
    /**
     * {@inheritdoc}
     */
    protected function action(): Response
    {
        $form = $this->getFormData();

        // update data validation
        $validator = new UpdateValidator();
        $validator->validateForm($form);

        $accessId = (int) $this->resolveArg($this::ACCESS_ID);

        $access = $this->accessRepository->byId($accessId);
        $access->update($form);

        $this->accessRepository->save($access);

        $this->logger->info("Access id ${accessId} has been updated.");

        return $this->respondWithData();
    }
}
