<?php

declare(strict_types=1);

namespace App\Application\Actions\Reservation;

use App\Application\Actions\Action;

use App\Domain\Reservation\IReservationRepository;
use Psr\Log\LoggerInterface;


abstract class ReservationAction extends Action
{

    protected IReservationRepository $reservations;

    /**
     * @param LoggerInterface $logger
     * @param IReservationRepository $reservations
     */
    public function __construct(
        LoggerInterface $logger,
        IReservationRepository $reservations
    ) {
        parent::__construct($logger);
        $this->reservations = $reservations;
    }
}
