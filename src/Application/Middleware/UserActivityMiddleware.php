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

use App\Domain\User\UserRepositoryInterface;
use Psr\Log\LoggerInterface;
use RuntimeException;



class UserActivityMiddleware extends BaseMiddleware
{
    public function __construct(
        private UserRepositoryInterface $userRepository,
        LoggerInterface $logger
    ) {
        parent::__construct($logger);
    }

    /**
     * {@inheritDoc}
     */
    public function processRequest(RequestHandler $handler): Response
    {
        $session = $this->request->getAttribute('session');
        $session && $this->userRepository->registerActivity((int) $session->userId);

        return $handler->handle($this->request);
    }
}
