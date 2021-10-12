<?php

declare(strict_types=1);

namespace App\Application\Actions\Request;

use Psr\Http\Message\ResponseInterface as Response;
use Slim\Exception\HttpBadRequestException;


class DeleteRequests extends RequestAction
{
    /**
     * {@inheritdoc}
     */
    public function action(): Response
    {
        $form = $this->getFormData();

        if (!isset($form->ids) || gettype($form->ids) !== 'array')
            throw new HttpBadRequestException($this->request, 'Body wiadomości musi zawierać parametr `ids` (typ array)');
               
        $this->requestRepository->deleteList($form->ids);


        $this->logger->info("User id {$this->session->userId} deleted {} images");

        return $this->respondWithData();
    }
}
