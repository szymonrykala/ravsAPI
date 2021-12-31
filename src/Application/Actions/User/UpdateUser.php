<?php

declare(strict_types=1);

namespace App\Application\Actions\User;

use Psr\Http\Message\ResponseInterface as Response;

use App\Domain\User\User;
use App\Domain\User\Validation\UpdateValidator;
use Slim\Exception\HttpUnauthorizedException;

class UpdateUser extends UserAction
{
    /**
     * {@inheritDoc}
     */
    public function action(): Response
    {
        $userId = (int) $this->resolveArg($this::USER_ID);

        $form = $this->getFormData();

        $validator = new UpdateValidator();
        $validator->validateForm($form);

        /** @var User $user */
        $user = $this->userRepository->byId($userId);

        $user->update($form);
        $this->userRepository->save($user);

        $this->logger->info("User id {$user->id} was updated");

        return $this->respondWithData();
    }
}
