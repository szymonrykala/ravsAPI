<?php

declare(strict_types=1);

namespace App\Application\Actions\Room;


use App\Application\Actions\Action;
use App\Domain\Room\IRoomRepository;
use Psr\Container\ContainerInterface;
use Psr\Log\LoggerInterface;


abstract class RoomAction extends Action
{
    protected IRoomRepository $roomRepository;


    public function __construct(ContainerInterface $di)
    {
        parent::__construct($di->get(LoggerInterface::class));

        $this->roomRepository = $di->get(IRoomRepository::class);
    }

    /**
     * Collects room_id and optional building_id from URI
     */
    protected function getUriParams(): array
    {
        $roomId = $this->resolveArg($this::ROOM_ID);
        $buildingId = $this->resolveArg($this::BUILDING_ID, FALSE);

        $params = ['id' => (int)  $roomId];
        $buildingId && $params['building'] = (int) $buildingId;

        return $params;
    }
}
