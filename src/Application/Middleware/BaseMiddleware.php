<?php

declare(strict_types=1);

namespace App\Application\Middleware;

use Psr\Log\LoggerInterface;


use Psr\Http\Message\{
    ResponseInterface as Response,
    ServerRequestInterface as Request
};

use Psr\Http\Server\{
    MiddlewareInterface as Middleware,
    RequestHandlerInterface as RequestHandler
};
use RuntimeException;
use stdClass;


abstract class BaseMiddleware implements Middleware
{
    /** HTTP request */
    protected Request $request;

    public function __construct(
        protected LoggerInterface $logger
    ) {
    }

    /**
     * Process an incoming server request
     */
    abstract protected function processRequest(RequestHandler $handler): Response;

    /**
     * abstracts away logic needed for all other middlewares
     */
    public function process(Request $request, RequestHandler $handler): Response
    {
        $this->logger->info($this::class . ' triggered');

        $this->request = $request;
        $this->handler = $handler;

        return $this->processRequest($handler);
    }

    /**
     * gets method of the request
     */
    protected function getMethod(): string
    {
        return $this->request->getMethod();
    }

    /**
     * gets path of the request
     */
    protected function getPath(): string
    {
        return $this->request->getUri()->getPath();
    }

    /**
     * returns session object with token data
     */
    protected function getSession():object
    {
        /** @var stdClass $session */
        $session = $this->request->getAttribute('session');
        if(!$session)
            throw new RuntimeException('Session is not set. Check order of the middlewares');
        
        return $session;
    }
}
