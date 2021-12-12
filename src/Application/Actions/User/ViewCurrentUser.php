<?php

declare(strict_types=1);

namespace App\Application\Actions\User;

use Psr\Http\Message\ResponseInterface as Response;


class ViewCurrentUser extends UserAction
{
    /**
     * {@inheritDoc}
     */
    protected function action(): Response
    {
        $userId = $this->session->userId;

        /** @var User $user */
        $user = $this->userRepository
            ->withAccess()
            ->byId($userId);

        $user->loadMetadata();

        $this->logger->info("User of id `${userId}` was viewed.");

        return $this->respondWithData($user);
    }
}
