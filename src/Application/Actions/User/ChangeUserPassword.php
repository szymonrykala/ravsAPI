<?php
declare(strict_types=1);

namespace App\Application\Actions\User;

use Psr\Http\Message\ResponseInterface as Response;

use App\Domain\User\User;


class ChangeUserPassword extends UserAction
{
    /**
     * {@inheritdoc}
     */
    public function action(): Response
    {
        $form = $this->getFormData();

        /** @var User $user */
        $user = $this->getUserByEmail($form->email);

        $user->unblock($form->code);
        $this->logger->info("User id {$user->id} was unblocked");

        
        $user->password = password_hash($form->newPassword, PASSWORD_BCRYPT);
        $this->userRepository->save($user);


        $this->logger->info("User id {$user->id} changed password");

        return $this->respondWithData("New password was set.");
    }
}