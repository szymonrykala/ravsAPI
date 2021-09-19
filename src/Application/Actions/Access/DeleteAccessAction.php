<?php

declare(strict_types=1);

namespace App\Application\Actions\Access;

use Psr\Http\Message\ResponseInterface as Response;


class DeleteAccessAction extends AccessAction
{
    /**
     * {@inheritdoc}
     */
    protected function action(): Response
    {
        $accessId = (int) $this->resolveArg('id');

        $access = $this->accessRepository->byId($accessId);
        $this->accessRepository->delete($access);

        $this->logger->info("Access id=${accessId} has been deleted.");

        return $this->respondWithData();
    }
}
