<?php
declare(strict_types=1);

namespace App\Application\Actions\User;

use Psr\Http\Message\ResponseInterface as Response;

use App\Utils\JWTFactory;



class AuthenticateUserAction extends UserAction
{
    /**
     * {@inheritdoc}
     */
    public function action(): Response
    {
        // $this->loadValidationSchema('User/login.json');
        $form = $this->getFormData();
        
        // $this->assertRequiredFields($form, [
        //     'email' => \string::class,
        //     'password' => \string::class,
        // ]);

        $user = $this->getUserByEmail($form->email);

        $user->login($form->password);

        $userToken = JWTFactory::generateToken($user);

        $this->logger->info("User id {$user->id} was authenticated");
        $this->userRepository->save($user);

        return $this->respondWithData($userToken);
    }
}