<?php

declare(strict_types=1);

namespace App\Application\Middleware;

use App\Infrastructure\TokenFactory\Exceptions\{
    TokenExpiredException,
    TokenNotValidException
};

use App\Infrastructure\TokenFactory\ITokenFactory;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;

use Slim\Exception\HttpUnauthorizedException;

use Psr\Log\LoggerInterface;

/**
 * Reads data from token provided from user
 * and passes it as a session to the request context
 */
class SessionMiddleware extends BaseMiddleware
{
    public function __construct(
        LoggerInterface $logger,
        private ITokenFactory $tokenFactory
    ) {
        parent::__construct($logger);
    }

    /**
     * {@inheritDoc}
     * @throws HttpUnauthorizedException
     */
    public function processRequest(RequestHandler $handler): Response
    {
        try {
            $tokenData = $this->tokenFactory->decode($this->getToken());
        } catch (TokenExpiredException $e) {
            throw new HttpUnauthorizedException($this->request, $e->getMessage());
        } catch (TokenNotValidException $e) {
            throw new HttpUnauthorizedException($this->request, $e->getMessage());
        }


        $this->request = $this->request->withAttribute('session', $tokenData);

        return $handler->handle($this->request);
    }


    /**
     * Gets token from authorization header
     * @throws HttpUnauthorizedException
     */
    private function getToken(): string
    {
        $auth = $this->request->getHeader('Authorization');

        if (!isset($auth[0])) {
            throw new HttpUnauthorizedException($this->request, "Brak nagłówka autoryzacji.");
        }

        return explode(' ', array_pop($auth))[1] ?? '';
    }
}
