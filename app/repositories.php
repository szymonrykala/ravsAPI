<?php
declare(strict_types=1);

use DI\ContainerBuilder;

use Psr\Container\ContainerInterface;

use App\Infrastructure\Database\DatabaseInterface;

use App\Domain\User\UserRepositoryInterface;
use App\Infrastructure\Repository\UserRepository;

use App\Domain\Image\ImageRepositoryInterface;
use App\Infrastructure\Repository\ImageRepository;

use App\Domain\Access\AccessRepositoryInterface;
use App\Infrastructure\Repository\AccessRepository;

use App\Domain\Request\RequestRepositoryInterface;
use App\Infrastructure\Repository\RequestRepository;

use App\Domain\Address\IAddressRepository;
use App\Infrastructure\Repository\AddressRepository;

use App\Domain\Building\IBuildingRepository;
use App\Infrastructure\Repository\BuildingRepository;

use App\Domain\Room\RoomRepositoryInterface;
use App\Infrastructure\Repository\RoomRepository;

use App\Domain\Reservation\IReservationRepository;
use App\Infrastructure\Repository\ReservationRepository;

use App\Domain\Configuration\IConfigurationRepository;
use App\Infrastructure\Repository\ConfigurationRepository;


return function (ContainerBuilder $containerBuilder) {
    // mapping interfaces to implementations
    $containerBuilder->addDefinitions([
        ImageRepositoryInterface::class => \DI\autowire(ImageRepository::class),
        AccessRepositoryInterface::class => \DI\autowire(AccessRepository::class),
        UserRepositoryInterface::class => \DI\autowire(UserRepository::class),
        RequestRepositoryInterface::class => \DI\autowire(RequestRepository::class),
        IAddressRepository::class => \DI\autowire(AddressRepository::class),
        IBuildingRepository::class => \DI\autowire(BuildingRepository::class),
        RoomRepositoryInterface::class => \DI\autowire(RoomRepository::class),
        IReservationRepository::class => \DI\autowire(ReservationRepository::class),
        IConfigurationRepository::class => \DI\autowire(ConfigurationRepository::class),
    ]);
};
