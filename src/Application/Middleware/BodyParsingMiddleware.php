<?php

declare(strict_types=1);

namespace App\Application\Middleware;

use Psr\Http\Message\{
    ResponseInterface as Response,
    ServerRequestInterface as Request
};

use Psr\Http\Server\{
    MiddlewareInterface as Middleware,
    RequestHandlerInterface as RequestHandler
};

use Slim\Exception\HttpBadRequestException;
use stdClass;


class BodyParsingMiddleware implements Middleware
{

    /**
     * {@inheritdoc}
     */
    public function process(Request $request, RequestHandler $handler): Response
    {

        if(in_array($request->getMethod(), ['POST', 'PATCH']) && !strpos($request->getUri()->getPath(), 'images')){

            $input = json_decode(file_get_contents('php://input'));

            if (json_last_error() !== JSON_ERROR_NONE) {
                throw new HttpBadRequestException($request, 'Malformed JSON input.');
            }
        }

        return $handler->handle($request->withParsedBody($input ?? new stdClass()));
    }
}