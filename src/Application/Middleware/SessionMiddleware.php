<?php

declare(strict_types=1);

namespace App\Application\Middleware;

use App\Application\Settings\SettingsInterface;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\MiddlewareInterface as Middleware;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;

use Slim\Exception\HttpUnauthorizedException;

use App\Utils\JWTFactory;
use App\Domain\Exception\DomainUnauthorizedOperationException;


/**
 * Reads data from token provided from user
 * and passes it as a session to the request context
 */
class SessionMiddleware implements Middleware
{
    private Request $request;
    private array $whiteList;



    public function __construct(SettingsInterface $settings)
    {
        $this->whiteList = $settings->get('authWhiteList');
    }

    /**
     * {@inheritDoc}
     */
    public function process(Request $request, RequestHandler $handler): Response
    {
        $this->request = $request;

        if (!in_array($this->request->getUri()->getPath(), $this->whiteList) && $this->request->getMethod()!=='OPTIONS') {
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
            throw new HttpUnauthorizedException($this->request, "Brak nagłówka autoryzacji.");
        }

        return explode(' ', array_pop($auth))[1] ?? '';
    }
}
