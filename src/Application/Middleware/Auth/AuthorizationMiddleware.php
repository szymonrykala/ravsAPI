<?php

declare(strict_types=1);

namespace App\Application\Middleware\Auth;


use App\Domain\Access\IAccessRepository;
use App\Domain\Reservation\IReservationRepository;
use App\Domain\Reservation\Reservation;
use App\Domain\User\User;
use App\Domain\User\IUserRepository;
use Psr\Log\LoggerInterface;


class AuthorizationMiddleware extends BaseAuthorizationMiddleware
{
    public function __construct(
        private IUserRepository $userRepository,
        private IReservationRepository $reservationRepository,
        IAccessRepository $accessRepository,
        LoggerInterface $logger
    ) {
        parent::__construct($accessRepository, $logger);
    }

    /**
     * {@inheritDoc}
     */
    protected function resolveAccess(): bool
    {
        $resolve = $this->getResolver();
        return $this->userAccess->owner || $resolve();
    }

    /**
     * returns user access resolver function for the user
     */
    private function getResolver(): callable
    {
        return match ($this->subject) {
            'resources', 'image', 'me' => fn () => TRUE,

            'requests' => fn () => $this->userAccess->logsAdmin,
            'accesses' => fn () => $this->userAccess->accessAdmin,
            'stats' => fn () => $this->userAccess->statsViewer,
            'keys' => fn () => $this->userAccess->keysAdmin,

            'addresses', 'buildings', 'rooms' => fn () => $this->resolvePremisesAccess(),
            'users' => fn () => $this->resolveUserAccess(),
            'reservations' => fn () => $this->resolveReservationAccess(),

            default => fn () => FALSE
        };
    }

    /**
     * validates if user has access to 'premises resources'
     */
    private function resolvePremisesAccess(): bool
    {
        $method = $this->getMethod();

        return $method === 'GET'
            || (in_array($method, ['POST', 'PATCH', 'DELETE']) && $this->userAccess->premisesAdmin);
    }

    /**
     * validates if user has access to reservations functionality
     */
    private function resolveReservationAccess(): bool
    {
        $method = $this->getMethod();

        if (
            ($method === 'GET' || $this->userAccess->reservationsAdmin)
            || ($method === 'POST' && $this->userAccess->reservationsAbility)
        ) return TRUE;

        // DELETE and PATCH
        /** @var Reservation $reservation */
        $reservation = $this->reservationRepository->byId($this->subjectId);

        return ((int) $this->getSession()->userId) === $reservation->userId;
    }

    /**
     * Validates if the user has access to edit user
     */
    private function resolveUserAccess(): bool
    {
        $method = $this->getMethod();

        if (in_array($method, ['PATCH', 'DELETE'])) {

            /** @var User $modifiedUser */
            $modifiedUser = $this->userRepository->byId($this->subjectId);

            return ((int) $this->getSession()->userId) === $modifiedUser;
        }
        return TRUE;
    }
}
