<?php
declare(strict_types=1);

namespace App\Application\Actions\Access;

use Psr\Http\Message\ResponseInterface as Response;



class ViewAccessAction extends AccessAction
{
    /**
     * {@inheritdoc}
     */
    protected function action(): Response
    {
        $accessId = (int) $this->resolveArg('id');

        $access = $this->accessRepository->byId($accessId);

        $this->logger->info("Access id=$access->id has been Viewed.");
        
        return $this->respondWithData($access);
    }
}