<?php

declare(strict_types=1);

namespace App\Infrastructure\Mailing;

use App\Domain\User\User;


interface IMailingService
{

    /**
     * Sets mail reciever
     */
    public function setReciever(User $user): void;

    /** 
     * Sets type of message to send
     */
    public function setMessageType(string $type): void;

    /**
     * Sends the email
     * @throws MailingServiceException
     */
    public function send(): void;

}
