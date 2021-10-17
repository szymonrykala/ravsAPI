<?php

declare(strict_types=1);

namespace App\Application\Middleware\Auth;

use App\Application\Middleware\BaseMiddleware;
use App\Domain\Access\Access;
use App\Domain\Access\AccessRepositoryInterface;
use Psr\Http\Message\ResponseInterface as Response;

use Psr\Http\Server\{
    RequestHandlerInterface as RequestHandler
};
use Psr\Log\LoggerInterface;
use Slim\Exception\HttpForbiddenException;

abstract class BaseAuthorizationMiddleware extends BaseMiddleware
{
    protected string $subject;
    protected ?int $subjectId = NULL;
    protected Access $userAccess;

    public function __construct(
        protected AccessRepositoryInterface $accessRepository,
        LoggerInterface $logger
    ) {
        parent::__construct($logger);
    }

    /** 
     * returns false if user doesn't have an access to operation
     */
    abstract protected function resolveAccess(): bool;

    /**
     * {@inheritDoc}
     */
    protected function processRequest(RequestHandler $handler): Response
    {
        $this->setRequestSubject();
        $session = $this->getSession();
        $this->userAccess = $this->accessRepository->byId($session->accessId);

        // if user is owner or access was resolved succesfully
        if ($this->userAccess->owner || $this->resolveAccess())
            return $handler->handle($this->request);

        throw new HttpForbiddenException($this->request, 'Nie masz wystarczających uprawnień by skorzystać z tej funkcji.');
    }

    /**
     * gets subject and subject id (if exists) from the request path
     * i.e: /users/1 => subject = 'users'; subjectId = 1
     */
    private function setRequestSubject(): void
    {
        $segments = explode('/', $this->getPath());

        $this->subject = array_pop($segments);

        if (is_numeric($this->subject)) {
            $this->subjectId = (int) $this->subject;
            $this->subject = array_pop($segments);
        }
    }
}
