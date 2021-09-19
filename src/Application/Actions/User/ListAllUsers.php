<?php

declare(strict_types=1);

namespace App\Application\Actions\User;

use Psr\Http\Message\ResponseInterface as Response;


class ListAllUsers extends UserAction
{

    /**
     * {@inheritdoc}
     */
    protected function action(): Response
    {
        // if deleted=1 => include deleted users
        $listDeleted = (int) $this->resolveQueryArg('deleted', FALSE);

        if(!$listDeleted) $this->userRepository->where([
            'deleted' => $listDeleted
        ]);

        $users = $this->userRepository->all();

        $this->logger->info("Users list was viewed.");

        return $this->respondWithData($users);
    }
}