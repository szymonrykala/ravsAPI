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
        // if deleted=1 - include deleted users
        $listDeleted = $this->resolveQueryArg('deleted', FALSE);
        $searchPhrase = $this->resolveQueryArg($this::SEARCH_STRING, FALSE);
        
        if(!$listDeleted) $this->userRepository->where([
            'deleted' => (int) $listDeleted
        ]);

        if($searchPhrase)
            $this->userRepository->search( (string) $searchPhrase);


        $users = $this->userRepository->all();

        $this->logger->info("Users list was viewed.");

        return $this->respondWithData($users);
    }
}
