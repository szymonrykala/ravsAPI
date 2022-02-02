<?php

declare(strict_types=1);

namespace App\Application\Actions\User;

use App\Domain\User\User;
use App\Domain\User\Validation\CreateValidator;
use App\Infrastructure\Mailing\IMailingService;
use App\Infrastructure\Mailing\MailingService;
use App\Infrastructure\Mailing\MailingServiceException;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface as Response;


class RegisterUser extends UserAction
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
    protected function action(): Response
    {
        $form = $this->getFormData();

        $validator = new CreateValidator();
        $validator->validateForm($form);

        $userId = $this->userRepository->register(
            $form->name,
            $form->surname,
            $form->email,
            $form->password
        );
        $this->logger->info("User id `${userId}` was registered.");

        /** @var User $user */
        $user = $this->userRepository->byId($userId);

        $this->mailer->setReciever($user);
        $this->mailer->setMessageType(MailingService::NEW_ACCOUNT);

        $message = 'Pomyślnie zarejestrowano';

        try {
            $this->mailer->send();
        } catch (MailingServiceException $e) {
            $user->activate($user->uniqueKey);
            $this->userRepository->save($user);

            $this->logger->error('Mailing service error while registering a user.');
            $message = 'Zarejestrowano bez potrzeby aktywacji - serwis mailowy niedostępny';
        }

        $this->logger->info("Mail to user id `${userId}` was send.");

        return $this->respondWithData($message, 201);
    }
}
