<?php

declare(strict_types=1);

namespace App\Application\Middleware;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use App\Domain\User\IUserRepository;
use Psr\Log\LoggerInterface;



class UserActivityMiddleware extends BaseMiddleware
{
    public function __construct(
        private IUserRepository $userRepository,
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

        // if sessin exists, log user's activity
        $session && $this->userRepository->registerActivity((int) $session->userId);

        return $handler->handle($this->request);
    }
}
