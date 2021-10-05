<?php

declare(strict_types=1);

namespace App\Application\Actions\User;

use Psr\Http\Message\ResponseInterface as Response;

use App\Domain\Reservation\IReservationRepository;
use App\Domain\User\UserRepositoryInterface;
use Psr\Container\ContainerInterface;
use Psr\Log\LoggerInterface;


class DeleteUser extends UserAction
{
    private IReservationRepository $reservationRepository;


    public function __construct(ContainerInterface $di)
    {
        parent::__construct($di);
        $this->reservationRepository = $di->get(IReservationRepository::class);
    }

    /**
     * {@inheritDoc}
     */
    public function action(): Response
    {
        $userId = (int) $this->resolveArg($this::USER_ID);
        $deletedUser = $this->userRepository->byId($userId);

        $this->userRepository->delete($deletedUser);

        $this->reservationRepository->deleteAllFutureUserReservations($deletedUser->id);

        $this->logger->info("User id {$deletedUser->id} was deleted");

        return $this->respondWithData();
    }
}
