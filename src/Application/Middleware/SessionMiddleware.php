<?php

declare(strict_types=1);

namespace App\Application\Middleware;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\MiddlewareInterface as Middleware;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;

use Slim\Exception\HttpUnauthorizedException;

use App\Utils\JWTFactory;
use App\Domain\Exception\DomainUnauthorizedOperationException;

class SessionMiddleware implements Middleware
{
    /** @var Request $request */
    private Request $request;

    /** @var array white list endpoints  */
    private array $whiteList = [
        '/users/auth', '/users/key', '/users/activate', '/users/password'
    ];

    /**
     * {@inheritdoc}
     */
    public function process(Request $request, RequestHandler $handler): Response
    {
        $this->request = $request;

        if (!in_array($this->request->getUri()->getPath(), $this->whiteList)) {

            try {
                $tokenData = JWTFactory::decode($this->getToken());
            } catch (DomainUnauthorizedOperationException $e) {
                throw new HttpUnauthorizedException($request, $e->getMessage());
            }

            $this->request = $this->request->withAttribute('session', $tokenData);
        }

        return $handler->handle($this->request);
    }

    private function getToken(): string
    {
        $auth = $this->request->getHeader('Authorization');

        if (!isset($auth[0])) {
            throw new HttpUnauthorizedException($this->request, "Authorization header is missing.");
        }

        return explode(' ', array_pop($auth))[1] ?? '';
    }
}
