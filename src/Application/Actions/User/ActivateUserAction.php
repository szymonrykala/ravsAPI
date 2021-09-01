<?php
declare(strict_types=1);

namespace App\Application\Actions\User;

use Psr\Http\Message\ResponseInterface as Response;

use App\Domain\User\Exceptions\UserNotActivatedException;

class ActivateUserAction extends UserAction
{
    /**
     * {@inheritdoc}
     */
    public function action(): Response
    {
        $form = $this->getFormData();
        
        $user = $this->getUserByEmail($form->email);

        $message = "";

        try{
            $user->login($form->password);
            $message = "User is already activated";

        }catch(UserNotActivatedException $ex){
            $user->activate($form->code);
            $this->logger->info("user with id {$user->id} was activated");
            $message = "User activation completed. Please login.";

        }finally{
            $this->userRepository->save($user);
        }

        return $this->respondWithData($message);
    }
}