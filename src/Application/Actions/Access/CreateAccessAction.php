<?php
declare(strict_types=1);

namespace App\Application\Actions\Access;

use Psr\Http\Message\ResponseInterface as Response;


class CreateAccessAction extends AccessAction
{
    /**
     * {@inheritdoc}
     */
    protected function action(): Response
    {
        $form = $this->getFormData();
 
        $accessId = $this->accessRepository->create(
            $form->name,
            $form->owner,
            $form->accessAdmin,
            $form->premisesAdmin,
            $form->keysAdmin,
            $form->reservationsAdmin,
            $form->reservationsAbility,
            $form->logsAdmin,
            $form->statsViewer
        );

        $this->logger->info("Access id=${accessId} has been created.");

        return $this->respondWithData($accessId, 201);
    }
}