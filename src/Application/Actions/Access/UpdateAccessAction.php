<?php
declare(strict_types=1);

namespace App\Application\Actions\Access;

use Psr\Http\Message\ResponseInterface as Response;



class UpdateAccessAction extends AccessAction
{
    /**
     * {@inheritdoc}
     */
    protected function action(): Response
    {
        $form = $this->getFormData();
        $accessId = (int) $this->resolveArg('id');

        $access = $this->accessRepository->byId($accessId);
        $access->update($form);

        $this->accessRepository->save($access);

        $this->logger->info("Access id ${accessId} has been updated.");
        
        return $this->respondWithData();
    }
}