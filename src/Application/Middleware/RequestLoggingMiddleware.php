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


class RequestLoggingMiddleware implements Middleware
{
    private RequestRepositoryInterface $requestRepository;

    public function __construct(RequestRepositoryInterface $requestRepository)
    {
        $this->requestRepository = $requestRepository;
    }


    /**
     * {@inheritDoc}
     */
    public function process(Request $request, RequestHandler $handler): Response
    {
        $timeStart = microtime(true);

        $response = $handler->handle($request);
                
        $processingTime = microtime(true) - $timeStart;

        $this->requestRepository->create(
            $request->getMethod(),
            $request->getUri()->getPath(),
            $request->getAttribute('session')->userId ?? NULL,
            $request->getParsedBody(),
            $processingTime
        );

        return $response;
    }
}