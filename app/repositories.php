<?php
declare(strict_types=1);

use DI\ContainerBuilder;


use App\Domain\User\IUserRepository;
use App\Infrastructure\Repository\UserRepository;

use App\Domain\Image\IImageRepository;
use App\Infrastructure\Repository\ImageRepository;

use App\Domain\Access\IAccessRepository;
use App\Infrastructure\Repository\AccessRepository;

use App\Domain\Request\IRequestRepository;
use App\Infrastructure\Repository\RequestRepository;

use App\Domain\Address\IAddressRepository;
use App\Infrastructure\Repository\AddressRepository;

use App\Domain\Building\IBuildingRepository;
use App\Infrastructure\Repository\BuildingRepository;

use App\Domain\Room\IRoomRepository;
use App\Infrastructure\Repository\RoomRepository;

use App\Domain\Reservation\IReservationRepository;
use App\Infrastructure\Repository\ReservationRepository;

use App\Domain\Configuration\IConfigurationRepository;
use App\Infrastructure\Repository\ConfigurationRepository;

use App\Domain\Stats\IStatsRepository;
use App\Infrastructure\Repository\StatsRepository;

return function (ContainerBuilder $containerBuilder) {
    // mapping interfaces to implementations
    $containerBuilder->addDefinitions([
        IImageRepository::class => \DI\autowire(ImageRepository::class),
        IAccessRepository::class => \DI\autowire(AccessRepository::class),
        IUserRepository::class => \DI\autowire(UserRepository::class),
        IRequestRepository::class => \DI\autowire(RequestRepository::class),
        IAddressRepository::class => \DI\autowire(AddressRepository::class),
        IBuildingRepository::class => \DI\autowire(BuildingRepository::class),
        IRoomRepository::class => \DI\autowire(RoomRepository::class),
        IReservationRepository::class => \DI\autowire(ReservationRepository::class),
        IConfigurationRepository::class => \DI\autowire(ConfigurationRepository::class),
        IStatsRepository::class => \DI\autowire(StatsRepository::class),
    ]);
};
