<?php

declare(strict_types=1);

namespace App\Application\Actions\User;

use App\Domain\User\Exceptions\BadCredentialsException;
use App\Domain\User\User;
use App\Domain\User\Validation\LoginValidator;
use App\Infrastructure\Mailing\IMailingService;
use App\Infrastructure\Mailing\MailingService;
use App\Infrastructure\TokenFactory\ITokenFactory;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Container\ContainerInterface;



class AuthenticateUser extends UserAction
{
    public function __construct(
        ContainerInterface $di,
        private ITokenFactory $tokenFactory,
        private IMailingService $mailer
    ) {
        parent::__construct($di);
    }


    /**
     * {@inheritDoc}
     */
    public function action(): Response
    {
        $form = $this->getFormData();

        $validator = new LoginValidator();
        $validator->validateForm($form);

        $user = $this->getUserByEmail($form->email);

        try {
            $previousBlockedState = $user->blocked;

            $user->login($form->password);
        } catch (BadCredentialsException $ex) {

            // if user could not authenticate to activate account, block the user
            if ($previousBlockedState === FALSE && $user->blocked) {
                $this->sendBlockNotification($user);
            }
            $this->userRepository->save($user);
            throw $ex;
        }

        $userToken = $this->tokenFactory->generateToken($user);

        $this->logger->info("User id {$user->id} was authenticated");
        $this->userRepository->save($user);

        // hide user password because of security and logging middleware
        $form->password = '************';

        $this->request->withParsedBody($form);
        return $this->respondWithData($userToken);
    }

    /**
     * Sends email notification to user about blockade
     */
    private function sendBlockNotification(User $user)
    {
        $this->mailer->setReciever($user);
        $this->mailer->setMessageType(
            MailingService::ACCOUNT_BLOCKED,
            ['reason' => "Zbyt duża ilość nieudanych prób logowania ({$user->loginFails})"]
        );
        $this->mailer->send();
    }
}
