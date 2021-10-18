<?php

declare(strict_types=1);

namespace App\Application\Actions\Key;

use App\Application\Actions\Action;
use App\Domain\Reservation\IReservationRepository;
use App\Domain\Room\IRoomRepository;
use Psr\Container\ContainerInterface;
use Psr\Log\LoggerInterface;



abstract class KeyAction extends Action
{
    protected IReservationRepository $reservationRepository;
    protected IRoomRepository $roomRepository;


    public function __construct(ContainerInterface $di)
    {
        parent::__construct($di->get(LoggerInterface::class));
        $this->reservationRepository = $di->get(IReservationRepository::class);
        $this->roomRepository = $di->get(IRoomRepository::class);
    }
}
