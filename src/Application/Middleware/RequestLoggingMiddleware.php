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

use App\Domain\Request\RequestRepositoryInterface;
use Psr\Log\LoggerInterface;


class RequestLoggingMiddleware extends BaseMiddleware
{
    public function __construct(
        private RequestRepositoryInterface $requestRepository,
        LoggerInterface $logger
    ) {
        parent::__construct($logger);
    }

    /**
     * {@inheritDoc}
     */
    public function processRequest(RequestHandler $handler): Response
    {
        $response = $handler->handle($this->request);

        $this->requestRepository->create($this->request);

        return $response;
    }
}
