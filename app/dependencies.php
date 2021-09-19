<?php
declare(strict_types=1);

use App\Application\Settings\SettingsInterface;
use DI\ContainerBuilder;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Monolog\Processor\UidProcessor;
use Psr\Container\ContainerInterface;
use Psr\Log\LoggerInterface;

use App\Infrastructure\Database;

use App\Application\Actions\IActionCache;
use App\Application\Actions\ActionMemoryCache;

use App\Domain\Reservation\Policy\ReservationCreatePolicy;

use function DI\autowire;

return function (ContainerBuilder $containerBuilder) {
    $containerBuilder->addDefinitions([
        LoggerInterface::class => function (ContainerInterface $c) {
            $settings = $c->get(SettingsInterface::class);

            $loggerSettings = $settings->get('logger');
            $logger = new Logger($loggerSettings['name']);

            $processor = new UidProcessor();
            $logger->pushProcessor($processor);

            $handler = new StreamHandler($loggerSettings['path'], $loggerSettings['level']);
            $logger->pushHandler($handler);

            return $logger;
        },

        IActionCache::class=>function(ContainerInterface $c){
            return new ActionMemoryCache([]);
        },

        Database\IDatabase::class => function(ContainerInterface $c) {
            $dbSettings = $c->get(SettingsInterface::class)->get('database');

            $database = new Database\MySQLDatabase(
                $dbSettings['user'],
                $dbSettings['password'],
                $dbSettings['host'],
                $dbSettings['name'],
            );

            return $database;
        },

        ReservationCreatePolicy::class => autowire(ReservationCreatePolicy::class)
    ]);
};
