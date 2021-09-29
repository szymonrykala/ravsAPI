<?php

declare(strict_types=1);

namespace App\Application\Actions\User;

use Psr\Http\Message\ResponseInterface as Response;
use Slim\Exception\HttpForbiddenException;


use App\Domain\User\User;
use App\Infrastructure\Mailing\IMailingService;
use App\Infrastructure\Mailing\MailingService;
use DateInterval;
use DateTime;
use Psr\Container\ContainerInterface;

class GenerateUserKey extends UserAction
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

        $user = $this->getUserByEmail($form->email);

        $_5minutes = new DateInterval('PT5M');

        $_timePassed = $user->lastGeneratedKeyDate
            ->add($_5minutes) < new DateTime('now');

        if ($_timePassed === FALSE) throw new HttpForbiddenException(
            $this->request,
            "Code can be generated each 5 minutes."
        );


        $this->assignUniqueKeyToUser($user);

        $this->userRepository->save($user);
        $this->logger->info("User with id {$user->id} has generated key");

        $this->mailer->setReciever($user);
        $this->mailer->setMessageType(MailingService::NEW_CODE_REQUEST);
        $this->mailer->send();

        return $this->respondWithData("Code has been send to Your mailbox.", 201);
    }

    /**
     * @param User &$user
     * @return void
     */
    private function assignUniqueKeyToUser(User $user): void
    {
        do {
            $user->assignUniqueKey();
        } while (!empty($this->userRepository
            ->where(['unique_key' => $user->uniqueKey])
            ->all()));
    }
}
