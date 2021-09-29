<?php

declare(strict_types=1);

namespace App\Application\Actions\Reservation;

use App\Application\Actions\Action;

use App\Domain\Reservation\IReservationRepository;
use Psr\Container\ContainerInterface;
use Psr\Log\LoggerInterface;


abstract class ReservationAction extends Action
{

    protected IReservationRepository $reservations;


    public function __construct(ContainerInterface $di)
    {
        parent::__construct($di->get(LoggerInterface::class));
        $this->reservations = $di->get(IReservationRepository::class);
    }
}
