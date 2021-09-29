<?php

declare(strict_types=1);

namespace App\Application\Actions\User;

use App\Domain\User\Exceptions\BadCredentialsException;
use App\Domain\User\Exceptions\UserBlockedException;
use App\Domain\User\User;
use App\Infrastructure\Mailing\IMailingService;
use App\Infrastructure\Mailing\MailingService;
use Psr\Http\Message\ResponseInterface as Response;

use App\Utils\JWTFactory;
use DateInterval;
use Psr\Container\ContainerInterface;

class AuthenticateUser extends UserAction
{

    private IMailingService $mailer;

    public function __construct(ContainerInterface $di)
    {
        parent::__construct($di);
        $this->mailer = $di->get(IMailingService::class);
    }


    /**
     * {@inheritdoc}
     */
    public function action(): Response
    {
        $form = $this->getFormData();

        $user = $this->getUserByEmail($form->email);

        try {

            $previousBlockedState = $user->blocked;

            $user->login($form->password);
        } catch (BadCredentialsException $ex) {

            // if user could not authenticate to activate account
            if ($previousBlockedState === FALSE && $user->blocked) {
                $this->mailer->setReciever($user);
                $this->mailer->setMessageType(
                    MailingService::ACCOUNT_BLOCKED,
                    ['reason' => "zbyt duża ilość nieudanych prób logowania ({$user->loginFails})"]
                );
                $this->mailer->send();
            }
            $this->userRepository->save($user);
            throw $ex;
            
        }catch(BadCredentialsException $ex){
            $this->userRepository->save($user);
            throw $ex;
        }

        $userToken = JWTFactory::generateToken($user);

        $this->logger->info("User id {$user->id} was authenticated");
        $this->userRepository->save($user);

        return $this->respondWithData($userToken);
    }
}
