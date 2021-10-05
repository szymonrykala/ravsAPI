<?php
declare(strict_types=1);

namespace App\Application\Actions\User;

use Psr\Http\Message\ResponseInterface as Response;



class UpdateUserAccess extends UserAction
{
    /**
     * {@inheritDoc}
     */
    public function action(): Response
    {

        $userId = (int) $this->resolveArg($this::USER_ID);
        $form = $this->getFormData();

        $user = $this->userRepository->byId($userId);

        $user->accessId = $form->accessId;

        $this->userRepository->save($user);

        $this->logger->info("User id {$user->id} changed access to id {$form->accessId}");

        return $this->respondWithData();
    }
}