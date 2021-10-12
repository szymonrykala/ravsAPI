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

        $subject = $this->resolveArg($this::REQUEST_SUBJECT);

        $searchParams['endpoint'] = '/' . $subject;

        if (isset($this->args['subject_id'])) {
            $searchParams['endpoint'] .= '/' . $this->resolveArg('subject_id');
        }


        $fields = [
            'user_id' => 'userId',
            'method' => 'method'
        ];

        foreach ($fields as $param => $field) {
            $value = $this->resolveQueryArg($field, FALSE);

            if ($value) {
                $searchParams[$param] = $_GET[$field];
            }
        }


        $requests = $this->requestRepository
            ->where($searchParams)
            ->setPagination($pagination)
            ->all();


        $this->logger->info("User id {$this->session->userId} listed requests");

        return $this->respondWithData($requests);
    }
}