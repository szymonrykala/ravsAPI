<?php
declare(strict_types=1);

namespace App\Application\Actions\Access;

use Psr\Http\Message\ResponseInterface as Response;



class ViewAccess extends AccessAction
{
    /**
     * {@inheritdoc}
     */
    protected function action(): Response
    {
        $accessId = (int) $this->resolveArg($this::ACCESS_ID);

        $access = $this->accessRepository->byId($accessId);

        $this->logger->info("Access id=$access->id has been Viewed.");
        
        return $this->respondWithData($access);
    }
}