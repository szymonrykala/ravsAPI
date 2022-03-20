<?php

declare(strict_types=1);

namespace App\Application\Actions\Image;

use App\Application\Actions\Action;
use App\Domain\Building\IBuildingRepository;
use App\Domain\Configuration\Configuration;
use App\Domain\Configuration\IConfigurationRepository;
use App\Domain\Image\IImageRepository;
use App\Domain\Room\IRoomRepository;
use App\Domain\User\IUserRepository;
use Psr\Container\ContainerInterface;
use Psr\Log\LoggerInterface;


abstract class ImageAction extends Action
{
    protected IImageRepository $imageRepository;
    protected IUserRepository $userRepository;
    protected IBuildingRepository $buildingRepository;
    protected IRoomRepository $roomRepository;
    protected Configuration $configuration;


    public function __construct(ContainerInterface $di)
    {
        parent::__construct($di->get(LoggerInterface::class));
        $this->imageRepository = $di->get(IImageRepository::class);
        $this->userRepository = $di->get(IUserRepository::class);
        $this->buildingRepository = $di->get(IBuildingRepository::class);
        $this->roomRepository = $di->get(IRoomRepository::class);
        $this->configuration = $di->get(IConfigurationRepository::class)->load();
    }


    /**
     * gets set of propper repository and object id 
     */
    protected function getPropperObjectSet(): array
    {
        $roomId = $this->resolveArg($this::ROOM_ID, FALSE);
        $buildingId = $this->resolveArg($this::BUILDING_ID, FALSE);
        $userId = $this->resolveArg($this::USER_ID, FALSE);

        // matching set of repository and resourec id for updateing image
        $set = match ('string') {
            gettype($roomId) => [$this->roomRepository, $roomId],
            gettype($userId) => [$this->userRepository, $userId],
            gettype($buildingId) => [$this->buildingRepository, $buildingId],
        };

        return $set;
    }
}
