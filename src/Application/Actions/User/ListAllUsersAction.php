<?php
declare(strict_types=1);

namespace App\Application\Actions\User;

use Psr\Http\Message\ResponseInterface as Response;

use App\Domain\Image\Image;


class ListAllUsersAction extends UserAction
{
    /**
     * {@inheritdoc}
     */
    protected function action(): Response
    {
        
        $users = $this->userRepository->all();

        $this->logger->info("Users list was viewed.");

        return $this->respondWithData($users);
    }
}
