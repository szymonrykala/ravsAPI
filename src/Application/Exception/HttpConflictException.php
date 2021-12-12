<?php

declare(strict_types=1);

namespace App\Application\Exception;

use Slim\Exception\HttpSpecializedException;

class HttpConflictException extends HttpSpecializedException
{
    public $code = 409;
    public $messaage = 'Wystąpił konflikt przez który nie można wykonać żądania.';
}
