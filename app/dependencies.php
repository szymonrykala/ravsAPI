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

use App\Domain\Reservation\Policy\ReservationCreatePolicy;
use App\Infrastructure\Database\PostgreSQL;
use App\Infrastructure\Mailing\{IMailingService, MailingService};
use App\Infrastructure\TokenFactory\{ITokenFactory, JWTFactory};
use Cloudinary\Cloudinary;

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

        Database\IDatabase::class => autowire(PostgreSQL::class),

        ITokenFactory::class => autowire(JWTFactory::class),

        IMailingService::class => autowire(MailingService::class),

        ReservationCreatePolicy::class => autowire(ReservationCreatePolicy::class),

        Cloudinary::class => function (ContainerInterface $c) {
            $cloudinarySettings = $c->get(SettingsInterface::class)->get('cloudinary');

            return new Cloudinary([
                'cloud' => [
                    'cloud_name' => $cloudinarySettings['cloudName'],
                    'api_key'  => $cloudinarySettings['key'],
                    'api_secret' => $cloudinarySettings['secret'],
                    'url' => [
                        'secure' => true
                    ]
                ]
            ]);
        }
    ]);
};
