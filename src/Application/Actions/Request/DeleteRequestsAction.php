<?php

declare(strict_types=1);

namespace App\Application\Actions\Request;

use Psr\Http\Message\ResponseInterface as Response;
use Slim\Exception\HttpBadRequestException;


class DeleteRequestsAction extends RequestAction
{
    /**
     * {@inheritdoc}
     */
    public function action(): Response
    {
        $form = $this->getFormData();

        if (!isset($form->ids) || gettype($form->ids) !== 'array')
            throw new HttpBadRequestException($this->request, 'You have to specify `ids` (type array) parameter');
               
        $this->requestRepository->deleteList($form->ids);


        $this->logger->info("User id {$this->session->userId} deleted {} images");

        return $this->respondWithData();
    }
}
