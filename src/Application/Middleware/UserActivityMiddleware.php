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


class UserActivityMiddleware implements Middleware
{
    private UserRepositoryInterface $userRepository;

    public function __construct(UserRepositoryInterface $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    /**
     * {@inheritDoc}
     */
    public function process(Request $request, RequestHandler $handler): Response
    {
        $userId = $request->getAttribute('session')->userId;
        $this->userRepository->registerActivity((int) $userId);

        return $handler->handle($request);
    }
}
