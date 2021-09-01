<?php
declare(strict_types=1);

namespace App\Application\Actions\User;

use Psr\Http\Message\ResponseInterface as Response;

class ViewCurrentUserAction extends UserAction
{
    /**
     * {@inheritdoc}
     */
    protected function action(): Response
    {
        $userId = $this->session->userId;

        /** @var User $user */
        $user = $this->userRepository->byId($userId);

        $user->loadMetadata();
        
        $user->image = $this->imageRepository->byId($user->imageId);
        $user->access = $this->accessRepository->byId($user->accessId);

        $this->logger->info("User of id `${userId}` was viewed.");

        return $this->respondWithData($user);
    }
}
