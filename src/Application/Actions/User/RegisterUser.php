<?php
declare(strict_types=1);

namespace App\Application\Actions\User;

use App\Infrastructure\Mailing\IMailingService;
use App\Infrastructure\Mailing\MailingService;
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
     * {@inheritdoc}
     */
    protected function action(): Response
    {   
        $form = $this->getFormData();

        $userId = $this->userRepository->register(
            $form->name,
            $form->surname,
            $form->email,
            $form->password
        );
        $this->logger->info("User id `${userId}` was registered.");

        $user = $this->userRepository->byId($userId);

        $this->mailer->setReciever($user);
        $this->mailer->setMessageType(MailingService::NEW_ACCOUNT);
        $this->mailer->send();

        $this->logger->info("Mail to user id `${userId}` was send.");

        return $this->respondWithData($userId, 201);
    }
}
