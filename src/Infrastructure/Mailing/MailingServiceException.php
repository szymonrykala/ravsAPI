<?php

declare(strict_types=1);

namespace App\Infrastructure\Mailing;

use Exception;

class MailingServiceException extends Exception
{
    public $code = 503;
    public $message = 'Serwis mailingowy napotkał niespodziewany błąd.';
}
