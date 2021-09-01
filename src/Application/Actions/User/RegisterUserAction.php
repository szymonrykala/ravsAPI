<?php
declare(strict_types=1);

namespace App\Application\Actions\User;

use Psr\Http\Message\ResponseInterface as Response;


class RegisterUserAction extends UserAction
{
    /**
     * {@inheritdoc}
     */
    protected function action(): Response
    {   
        $form = $this->getFormData();

        $userId = $this->userRepository->register(
            $form->name,
            $form->surname,
            $form->email,
            $form->password
        );
 
        $this->logger->info("User id `${userId}` was registered.");

        return $this->respondWithData($userId, 201);
    }
}
