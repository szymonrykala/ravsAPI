<?php

declare(strict_types=1);

namespace App\Application\Middleware;

use App\Application\Settings\SettingsInterface;
use App\Domain\Access\Access;
use App\Domain\Access\AccessRepositoryInterface;
use App\Domain\Exception\DomainUnauthorizedOperationException;
use App\Domain\Reservation\IReservationRepository;
use App\Domain\User\UserRepositoryInterface;
use Psr\Http\Message\{
    ResponseInterface as Response,
    ServerRequestInterface as Request
};

use Psr\Http\Server\{
    MiddlewareInterface as Middleware,
    RequestHandlerInterface as RequestHandler
};



class AuthorizationMiddleware implements Middleware
{

    private AccessRepositoryInterface $accessRepository;
    private UserRepositoryInterface $userRepository;
    private IReservationRepository $reservationRepository;

    private Access $access;
    private Request $request;

    /** List of endpoints that don't need to be authorized */
    private array $whiteList;

    /** subject of the request i.ex. 'rooms' */
    private string $subject;

    /** id of the request subject i.ex. '12' */
    private int $subjectId;


    public function __construct(
        AccessRepositoryInterface $accessRepository,
        UserRepositoryInterface $userRepository,
        IReservationRepository $reservationRepository,
        SettingsInterface $settings
    ) {
        $this->accessRepository = $accessRepository;
        $this->reservationRepository = $reservationRepository;
        $this->userRepository = $userRepository;
        $this->whiteList = $settings->get('authWhiteList');
    }


    /**
     * {@inheritDoc}
     */
    public function process(Request $request, RequestHandler $handler): Response
    {
        $this->request = $request;

        if (!in_array($this->request->getUri()->getPath(), $this->whiteList) && $this->request->getMethod()!=='OPTIONS' ) {
            $session = $this->request->getAttribute('session');
            $this->access = $access = $this->accessRepository->byId((int) $session->accessId);

            if (!$access->owner) {
                $this->setUpRequestSegments();
                $resolve = $this->getResolver();

                // resolving access of the user have to be true
                if ($resolve() === FALSE)
                    throw new DomainUnauthorizedOperationException();
            }
        }

        return $handler->handle($this->request);
    }

    /** splits request URI path to array of segments */
    private function getRequestSegments(): array
    {
        return explode('/', $this->request->getUri()->getPath());
    }

    /** Sets the $subject and $subjectId properties */
    private function setUpRequestSegments(): void
    {
        $segments = $this->getRequestSegments();

        $id = NULL;
        $subject = NULL;

        $subject = array_pop($segments);
        if (is_numeric($subject)) {
            $id = $subject;
            $subject = array_pop($segments);
        }
        $this->subjectId = (int) $id;
        $this->subject = $subject;
    }

    /**
     * returns user access resolver function for the user
     */
    private function getResolver(): callable
    {
        return [
            'images' => fn () => TRUE,
            'requests' => fn () => $this->access->logsAdmin,
            'accesses' => fn () => $this->access->accessAdmin,
            'addresses' => fn () => $this->resolvePremisesAccess(),
            'resources' => fn () => TRUE,
            'buildings' => fn () => $this->resolvePremisesAccess(),
            'rooms' => fn () => $this->resolvePremisesAccess(),

            'users' => fn () => $this->resolveUserAccess(),
            'me' => fn () => TRUE,
            'reservations' => fn () => $this->resolveReservationAccess(),
            'stats' => fn () => $this->access->statsViewer,

            'configurations' => fn () => $this->access->owner,
            'keys' => fn () => $this->access->keysAdmin
        ][$this->subject];
    }

    /**
     * validates if user has access to 'premises resources'
     */
    private function resolvePremisesAccess(): bool
    {
        $method = $this->request->getMethod();
        if ($method === 'GET') {
            return TRUE;
        }

        return in_array($method, ['POST', 'PATCH', 'DELETE']) && $this->access->premisesAdmin;
    }

    /**
     * validates if user has access to reservations functionality
     */
    private function resolveReservationAccess(): bool
    {
        $method = $this->request->getMethod();

        if ($method === 'GET' || $this->access->reservationsAdmin) return TRUE;

        if ($method === 'POST' && $this->access->reservationsAbility)
            return TRUE;

        // DELETE and PATCH
        $sessionUserId = (int) $this->request->getAttribute('session')->userId;
        $reservation = $this->reservationRepository->byId($this->subjectId);

        return $sessionUserId === $reservation->userId;
    }

    /**
     * Validates if the user has access to edit user
     */
    private function resolveUserAccess(): bool
    {
        $method = $this->request->getMethod();

        if (in_array($method, ['PATCH', 'DELETE'])) {
            $sessionUserId = $this->request->getAttribute('session')->userId;
            $modifiedUser = $this->userRepository->byId($this->subjectId);

            return $sessionUserId === $modifiedUser;
        }
        return TRUE;
    }
}
