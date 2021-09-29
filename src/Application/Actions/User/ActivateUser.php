<?php

declare(strict_types=1);

namespace App\Application\Actions\User;

use App\Domain\User\Exceptions\BadCredentialsException;
use App\Domain\User\Exceptions\UserBlockedException;
use Psr\Http\Message\ResponseInterface as Response;

use App\Domain\User\Exceptions\UserNotActivatedException;
use App\Infrastructure\Mailing\IMailingService;
use App\Infrastructure\Mailing\MailingService;
use phpDocumentor\Reflection\Types\Boolean;
use Psr\Container\ContainerInterface;



class ActivateUser extends UserAction
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
        $this->mailer->setReciever($user);

        $message = "";

        $blockedBefore = $user->blocked;

        try {
            $user->login($form->password);
            $message = "User is already activated";
        } catch (BadCredentialsException $ex) {

            // if user could not authenticate to activate account
            if ($blockedBefore === FALSE && $user->blocked) {
                $this->mailer->setMessageType(
                    MailingService::ACCOUNT_BLOCKED,
                    ['reason' => "zbyt duża ilość nieudanych prób autoryzacji ({$user->loginFails}) podczas aktywacji konta"]
                );
                $this->mailer->send();
            }
            $this->userRepository->save($user);
            throw $ex;
        } catch (UserNotActivatedException $ex) {
            $user->activate($form->code);
            $this->logger->info("user with id {$user->id} was activated");
            $message = "User activation completed. Please login.";

            $this->mailer->setMessageType(MailingService::ACCOUNT_ACTIVATED);
            $this->mailer->send();
        } finally {
            $this->userRepository->save($user);
        }

        return $this->respondWithData($message);
    }
}
