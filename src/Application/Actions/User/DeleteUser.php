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
     * {@inheritdoc}
     */
    public function action(): Response
    {
        // Get user from token and check is it the same user or 
        // he has privilages to do this
        // $access = $this->accessRepository->byId(); // access of user who perform deleting

        $userId = (int) $this->resolveArg('user_id');
        $deletedUser = $this->userRepository->byId($userId);

        $this->userRepository->delete($deletedUser);

        $this->reservationRepository->deleteAllFutureUserReservations($deletedUser->id);

        $this->logger->info("User id {$deletedUser->id} was deleted");

        return $this->respondWithData();
    }
}
