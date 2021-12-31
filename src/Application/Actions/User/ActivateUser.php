<?php

declare(strict_types=1);

namespace App\Application\Actions\User;

use App\Application\Exception\HttpConflictException;
use App\Domain\User\Exceptions\BadCredentialsException;
use Psr\Http\Message\ResponseInterface as Response;

use App\Domain\User\Exceptions\UserNotActivatedException;
use App\Domain\User\Validation\ActivateValidator;
use App\Infrastructure\Mailing\IMailingService;
use App\Infrastructure\Mailing\MailingService;
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
     * {@inheritDoc}
     */
    public function action(): Response
    {
        $form = $this->getFormData();

        $validator = new ActivateValidator();
        $validator->validateForm($form);

        $user = $this->getUserByEmail($form->email);
        $this->mailer->setReciever($user);

        $message = "";

        $blockedBefore = $user->blocked;

        try {
            $user->login($form->password);

            throw new HttpConflictException($this->request, 'Użytkownik jest już aktywny.');
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
            $message = "Aktywacja przebiegła pomyślnie. Zaloguj się!";

            $this->mailer->setMessageType(MailingService::ACCOUNT_ACTIVATED);
            $this->mailer->send();
        } finally {
            $this->userRepository->save($user);
        }

        // hide user password because of security and logging middleware
        $form->password = '************';

        $this->request->withParsedBody($form);
        return $this->respondWithData($message);
    }
}
