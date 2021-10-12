<?php
declare(strict_types=1);

namespace App\Domain\Exception;

use Slim\Exception\HttpException;


class HttpUnprocessableEntityException extends HttpException
{
    public $message = "Nie można przetworzyć zapytania.";
    public $code = 422;
}