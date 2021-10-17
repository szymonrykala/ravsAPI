<?php

declare(strict_types=1);

namespace App\Application\Middleware;

use Psr\Http\Message\{
    ResponseInterface as Response,
};

use Psr\Http\Server\{
    RequestHandlerInterface as RequestHandler
};

use Slim\Exception\HttpBadRequestException;
use stdClass;


final class BodyParsingMiddleware extends BaseMiddleware
{

    /**
     * {@inheritDoc}
     */
    public function processRequest(RequestHandler $handler): Response
    {
        $content = file_get_contents('php://input');
        if(!empty($content)){
            $input = json_decode($content);

            if (json_last_error() !== JSON_ERROR_NONE) {
                throw new HttpBadRequestException($this->request, 'Nieprawidłowy format wiadomości. Użyj formatu JSON.');
            }
        }

        return $handler->handle($this->request->withParsedBody($input ?? new stdClass()));
    }
}