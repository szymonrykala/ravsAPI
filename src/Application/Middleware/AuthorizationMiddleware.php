<?php

declare(strict_types=1);

namespace App\Application\Middleware;

use App\Domain\Access\Access;
use App\Domain\Access\AccessRepositoryInterface;
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


    /**
     * @param AccessRepositoryInterface accessRepository
     * @param UserRepositoryInterface userRepository
     * @param IReservationRepository reservationRepository
     */
    public function __construct(
        AccessRepositoryInterface $accessRepository,
        UserRepositoryInterface $userRepository,
        IReservationRepository $reservationRepository
    ) {
        $this->accessRepository = $accessRepository;
        $this->reservationRepository = $reservationRepository;
        $this->userRepository = $userRepository;
    }

    /**
     * {@inheritdoc}
     */
    public function process(Request $request, RequestHandler $handler): Response
    {
        $this->request = $request;

        $session = $request->getAttribute('session');
        $this->access = $access = $this->accessRepository->byId($session->accessId);

        if (!$access->owner) {
            $resolve = $this->getResolverFunction();

            if ($resolve() === FALSE)
                throw new AuthorizationMiddlewareException();
        }
        return $handler->handle($request);
    }

    /**
     * @return callable resolver function
     */
    private function getResolverFunction(): callable
    {
        $segments = $this->getRequestSegments();

        $id = NULL;
        $subject = NULL;

        $subject = array_pop($segments);
        if (is_numeric($subject)) {
            $id = $subject;
            $subject = array_pop($segments);
        }

        /** @var callable $resolver */
        $resolver = $this->getFilledResolversTable()[$subject];

        $this->subjectId = $id;
        return $resolver;
    }

    /**
     * @return array
     */
    private function getFilledResolversTable(): array
    {
        return [
            'requests' => fn () => $this->access->logsAdmin,
            'access' => fn () => $this->access->accessAdmin,
            'address' => $this->resolvePremisesAccess,
            'buildings' => $this->resolvePremisesAccess,
            'rooms' => $this->resolvePremisesAccess,

            'users' => $this->resolveUserAccess,
            'reservations' => $this->resolveReservationAccess,
            'stats' => fn () => $this->access->statsViewer,

            'configurations' => fn () => $this->access->owner,
            'keys' => fn () => $this->access->keysAdmin
        ];
    }

    /**
     * @return bool
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
     * @return bool
     */
    private function resolveReservationAccess(): bool
    {
        $method = $this->request->getMethod();

        if ($method === 'GET' || $this->access->reservationsAdmin) return TRUE;

        if ($method === 'POST' && $this->access->reservationsAbility)
            return TRUE;
        else return FALSE;

        // DELETE and PATCH
        $sessionUserId = $this->request->getAttribute('session')->userId;
        $reservation = $this->reservationRepository->byId($this->subjectId);

        return $sessionUserId === $reservation->userId;
    }

    /**
     * @return bool
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


    /**
     * @return array
     */
    private function getRequestSegments(): array
    {
        return explode('/', $this->request->getUri()->getPath());
    }
}
