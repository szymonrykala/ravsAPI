<?php
declare(strict_types=1);

namespace App\Application\Actions\User;

use Psr\Http\Message\ResponseInterface as Response;

use App\Domain\User\User;


class UpdateUserAccess extends UserAction
{
    /**
     * {@inheritdoc}
     */
    public function action(): Response
    {

        $userId = (int) $this->resolveArg('user_id');
        $form = $this->getFormData();

        /** @var User $user */
        $user = $this->userRepository->byId($userId);


        $user->accessId = $form->accessId;

        $this->userRepository->save($user);

        $this->logger->info("User id {$user->id} changed access to id {$form->accessId}");

        return $this->respondWithData();
    }
}