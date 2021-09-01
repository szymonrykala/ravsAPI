<?php
declare(strict_types=1);

namespace App\Application\Actions\User;

use Psr\Http\Message\ResponseInterface as Response;

use App\Domain\User\User;
use Slim\Exception\HttpForbiddenException;

class UpdateUserAccessAction extends UserAction
{
    /**
     * {@inheritdoc}
     */
    public function action(): Response
    {

        $userId = (int) $this->resolveArg('userId');
        $form = $this->getFormData();

        /** @var User $user */
        $user = $this->userRepository->byId($userId);

        /** @var Access $sessionAccess */
        $sessionAccess = $this->accessRepository->byId($this->session->accessId);
        
        if($sessionAccess->accessEdit === FALSE){
            throw new HttpForbiddenException(
                $this->request,
                "You cannot change the access permissions."
            );
        }

        $user->accessId = $form->accessId;

        $this->userRepository->save($user);

        $this->logger->info("User id {$user->id} changed access to id {$form->accessId}");

        return $this->respondWithData();
    }
}