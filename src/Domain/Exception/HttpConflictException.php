<?php

declare(strict_types=1);

namespace App\Domain\Exception;

use Slim\Exception\HttpException;

class HttpConflictException extends HttpException
{
    public $code = 409;
}
