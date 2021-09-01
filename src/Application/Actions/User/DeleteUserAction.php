<?php
declare(strict_types=1);

namespace App\Application\Actions\User;

use Psr\Http\Message\ResponseInterface as Response;



class DeleteUserAction extends UserAction
{
    /**
     * {@inheritdoc}
     */
    public function action(): Response
    {
        // Get user from token and check is it the same user or 
        // he has privilages to do this
        // $access = $this->accessRepository->byId(); // access of user who perform deleting

        $userId = (int) $this->resolveArg('userId');
        $deletedUser = $this->userRepository->byId($userId);

        $this->userRepository->delete($deletedUser);

        $this->logger->info("User id {$deletedUser->id} was deleted");

        return $this->respondWithData();
    }
}