<?php

declare(strict_types=1);

namespace App\Application\Actions\Access;

use Psr\Http\Message\ResponseInterface as Response;


class DeleteAccess extends AccessAction
{
    
    /**
     * {@inheritdoc}
     */
    protected function action(): Response
    {
        $accessId = (int) $this->resolveArg($this::ACCESS_ID);

        $access = $this->accessRepository->byId($accessId);
        $this->accessRepository->delete($access);

        $this->logger->info("Access id=${accessId} has been deleted.");

        return $this->respondWithData();
    }
}
