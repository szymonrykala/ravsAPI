<?php
declare(strict_types=1);

namespace App\Application\Actions\Access;

use Psr\Http\Message\ResponseInterface as Response;



class ListAllAccesses extends AccessAction
{
    /**
     * {@inheritdoc}
     */
    protected function action(): Response
    {
        $items = $this->accessRepository->orderBy('id')->all();

        $this->logger->info("All Accesses has been viewed.");

        return $this->respondWithData($items);
    }
}

