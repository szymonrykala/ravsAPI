<?php

declare(strict_types=1);

use App\Application\Settings\Settings;
use App\Application\Settings\SettingsInterface;
use DI\ContainerBuilder;


return function (ContainerBuilder $containerBuilder) {

    // Global Settings Object
    $containerBuilder->addDefinitions([
        SettingsInterface::class => function () {
            return new Settings([
                'displayErrorDetails' => boolval($_ENV['DISPLAY_ERROR_DETAILS'] ?? false), // Should be set to false in production
                'logError'            => boolval($_ENV['LOG_ERROR_DETAILS'] ?? false),
                'logErrorDetails'     => boolval($_ENV['LOG_ERROR'] ?? false),
                'logger' => [
                    'name' => 'ravs_api',
                    'path' => $_ENV['LOG_PATH'] ?? 'php://stdout',
                    'level' => $_ENV['LOGGER_LEVEL'],
                ],
                'database' => [
                    'user' => $_ENV['DB_USER'],
                    'password' => $_ENV['DB_PASSWORD'],
                    'host' => $_ENV['DB_HOST'],
                    'name' => $_ENV['DB_NAME']
                ],
                'smtp' => [
                    'host' => $_ENV['SMTP_HOST'],
                    'port' => $_ENV['SMTP_PORT'],
                    'username' => $_ENV['SMTP_USER'],
                    'password' => $_ENV['SMTP_PASSWORD'],
                    'mailerName' => 'Rav System',
                    'debug' => (int) $_ENV['SMTP_DEBUG'] ?? 0
                ]
            ]);
        }
    ]);
};
