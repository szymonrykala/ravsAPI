<?php
declare(strict_types=1);

namespace App\Application\Exception;

use Slim\Exception\HttpSpecializedException;


class HttpUnprocessableEntityException extends HttpSpecializedException
{
    public $message = "Nie można przetworzyć zapytania.";
    public $code = 422;
}