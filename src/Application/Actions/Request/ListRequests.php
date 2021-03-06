<?php

declare(strict_types=1);

namespace App\Application\Actions\Request;

use Psr\Http\Message\ResponseInterface as Response;

class ListRequests extends RequestAction
{
    /**
     * {@inheritDoc}
     */
    public function action(): Response
    {
        $pagination = $this->preparePagination();

        $searchParams = [];

        $searchParams['endpoint'] = str_replace('/requests', '', $this->request->getUri()->getPath());

        $fields = [
            'user_id' => 'userId',
            'method' => 'method',
            'endpoint' => 'endpoint'
        ];

        foreach ($fields as $param => $field) {
            $value = $this->resolveQueryArg($field, FALSE);

            if ($value) {
                // decoding query string values
                $searchParams[$param] = urldecode($_GET[$field]);
            }
        }


        $requests = $this->requestRepository
            ->whereLIKE($searchParams)
            ->setPagination($pagination)
            ->all();


        $this->logger->info("User id {$this->session->userId} listed requests");

        return $this->respondWithData($requests);
    }
}
