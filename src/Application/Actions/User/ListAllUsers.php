<?php

declare(strict_types=1);

namespace App\Application\Actions\User;

use Psr\Http\Message\ResponseInterface as Response;


class ListAllUsers extends UserAction
{

    /**
     * {@inheritDoc}
     */
    protected function action(): Response
    {
        $pagination = $this->preparePagination();

        $accessId = $this->resolveQueryArg('accessId', FALSE);
        $activated = $this->resolveQueryArg('activated', FALSE);

        // if deleted=1 - include deleted users
        $listDeleted = $this->resolveQueryArg('deleted', FALSE);
        $searchPhrase = $this->resolveQueryArg($this::SEARCH_STRING, FALSE);

        $params = [];

        $accessId && $params['access'] = (int) $accessId;
        $activated && $params['activated'] =  $this->strToBool($activated);


        if ($listDeleted && $this->strToBool($listDeleted) === FALSE) {
            $params['deleted'] = FALSE;
        }
        
        if ($searchPhrase)
            $this->userRepository->search((string) $searchPhrase);
        
        if (!empty($params)) $this->userRepository->where($params);



        $users = $this->userRepository
            ->setPagination($pagination)
            ->all();

        $this->logger->info("Users list was viewed.");

        return $this->respondWithData($users);
    }

    private function strToBool(string $val): bool
    {
        $val = strtoupper($val);
        return $val === 'TRUE';
    }
}
