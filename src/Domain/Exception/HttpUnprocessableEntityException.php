<?php
declare(strict_types=1);

namespace App\Domain\Exception;

use Slim\Exception\HttpException;


class HttpUnprocessableEntityException extends HttpException
{
    public $message = "Unprocessable payload";
    public $code = 422;
}