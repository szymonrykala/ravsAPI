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

use stdClass;

class RequestLoggingMiddleware implements Middleware
{

    public function __construct(RequestRepositoryInterface $requestRepository)
    {
        $this->requestRepository = $requestRepository;
    }


    /**
     * {@inheritdoc}
     */
    public function process(Request $request, RequestHandler $handler): Response
    {
        $timeStart = microtime(true);

        /** @var Response $response */
        $response = $handler->handle($request);

        /** @var stdClass $session */
        $session = $request->getAttribute('session');
        
        $processingTime = microtime(true) - $timeStart;

        $session && $this->requestRepository->create(
            $request->getMethod(),
            $request->getUri()->getPath(),
            $request->getAttribute('session')->userId,
            $request->getParsedBody(),
            $processingTime
        );

        return $response;
    }
}