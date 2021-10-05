<?php

declare(strict_types=1);

namespace App\Application\Actions\Key;

use App\Application\Actions\Action;
use App\Domain\Reservation\IReservationRepository;
use App\Domain\Room\RoomRepositoryInterface;
use Psr\Container\ContainerInterface;
use Psr\Log\LoggerInterface;



abstract class KeyAction extends Action
{
    protected IReservationRepository $reservationRepository;
    protected RoomRepositoryInterface $roomRepository;


    public function __construct(ContainerInterface $di)
    {
        parent::__construct($di->get(LoggerInterface::class));
        $this->reservationRepository = $di->get(IReservationRepository::class);
        $this->roomRepository = $di->get(RoomRepositoryInterface::class);
    }
}
