<?php

declare(strict_types=1);

namespace App\Application\Actions\User;

use Psr\Http\Message\ResponseInterface as Response;

use App\Domain\User\User;
use Slim\Exception\HttpUnauthorizedException;

class UpdateUser extends UserAction
{
    /**
     * {@inheritDoc}
     */
    public function action(): Response
    {
        $session = $this->request->getAttribute('session');

        $userId = (int) $this->resolveArg($this::USER_ID);

        $form = $this->getFormData();

        /** @var User $user */
        $user = $this->userRepository->byId($userId);

        if ($user->isSessionUser($session) === FALSE) {
            throw new HttpUnauthorizedException(
                $this->request,
                'Nie możesz aktualizować tego użytkownika'
            );
        }

        $user->update($form);
        $this->userRepository->save($user);

        $this->logger->info("User id {$user->id} was updated");

        return $this->respondWithData();
    }
}
