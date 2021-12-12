<?php

declare(strict_types=1);

namespace App\Application\Actions\User;

use Psr\Http\Message\ResponseInterface as Response;
use Slim\Exception\HttpForbiddenException;
use Psr\Container\ContainerInterface;

use App\Domain\User\User;
use App\Infrastructure\Mailing\IMailingService;
use App\Infrastructure\Mailing\MailingService;
use App\Utils\JsonDateTime;



class GenerateUserKey extends UserAction
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

        $user = $this->getUserByEmail($form->email);

        $_timePassed = $user->lastGeneratedKeyDate > new JsonDateTime('5 minutes ago');


        if ($_timePassed) throw new HttpForbiddenException(
            $this->request,
            "Kod może być generowany w odstępie 5 minut."
        );


        $user->assignUniqueKey();

        $this->userRepository->save($user);
        $this->logger->info("User with id {$user->id} has generated key");

        $this->mailer->setReciever($user);
        $this->mailer->setMessageType(MailingService::NEW_CODE_REQUEST);
        $this->mailer->send();

        return $this->respondWithData("Kod został wysłany na adres email.", 201);
    }
}
