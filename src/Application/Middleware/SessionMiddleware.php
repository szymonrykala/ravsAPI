<?php

declare(strict_types=1);

namespace App\Application\Middleware;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;

use Slim\Exception\HttpUnauthorizedException;

use App\Utils\JWTFactory;
use App\Utils\JWTException;


/**
 * Reads data from token provided from user
 * and passes it as a session to the request context
 */
class SessionMiddleware extends BaseMiddleware
{

    /**
     * {@inheritDoc}
     */
    public function processRequest(RequestHandler $handler): Response
    {
        try {
            $tokenData = JWTFactory::decode($this->getToken());
        } catch (JWTException $e) {
            throw new HttpUnauthorizedException($this->request, $e->getMessage());
        }

        $this->request = $this->request->withAttribute('session', $tokenData);

        return $handler->handle($this->request);
    }

    private function getToken(): string
    {
        $auth = $this->request->getHeader('Authorization');

        if (!isset($auth[0])) {
            throw new HttpUnauthorizedException($this->request, "Brak nagłówka autoryzacji.");
        }

        return explode(' ', array_pop($auth))[1] ?? '';
    }
}
