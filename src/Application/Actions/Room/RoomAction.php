<?php
declare(strict_types=1);

namespace App\Application\Actions\Room;


use App\Application\Actions\Action;
use App\Domain\Room\RoomRepositoryInterface;


use Psr\Log\LoggerInterface;


abstract class RoomAction extends Action
{
    protected $roomRepository;
    protected $buildingRepository;

    /**
     * @param LoggerInterface $logger
     * @param RoomRepositoryInterface $userRepository
     */
    public function __construct(
        LoggerInterface $logger,
        RoomRepositoryInterface $roomRepository
    ) {
        parent::__construct($logger);

        $this->roomRepository = $roomRepository;
    }
}

