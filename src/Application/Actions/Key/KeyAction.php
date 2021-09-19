<?php

declare(strict_types=1);

namespace App\Application\Actions\Key;

use App\Application\Actions\Action;
use App\Domain\Reservation\IReservationRepository;
use App\Domain\Room\RoomRepositoryInterface;
use Psr\Log\LoggerInterface;



abstract class KeyAction extends Action
{
    protected IReservationRepository $reservationRepository;
    protected RoomRepositoryInterface $roomRepository;



    public function __construct(
        LoggerInterface $logger,
        IReservationRepository $reservationRepository,
        RoomRepositoryInterface $roomRepository
    ) {
        parent::__construct($logger);
        $this->reservationRepository = $reservationRepository;
        $this->roomRepository = $roomRepository;
    }
}
