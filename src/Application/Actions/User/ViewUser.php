<?php
declare(strict_types=1);

namespace App\Application\Actions\User;

use Psr\Http\Message\ResponseInterface as Response;

class ViewUser extends UserAction
{
    /**
     * {@inheritdoc}
     */
    protected function action(): Response
    {
        $userId = (int) $this->resolveArg('user_id');

        $user = $this->userRepository
                    ->withAccess()
                    ->byId($userId);

        $this->logger->info("User of id `${userId}` was viewed.");

        return $this->respondWithData($user);
    }
}
