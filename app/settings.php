<?php

declare(strict_types=1);

use App\Application\Settings\Settings;
use App\Application\Settings\SettingsInterface;
use DI\ContainerBuilder;
use Monolog\Logger;

return function (ContainerBuilder $containerBuilder) {

    // Global Settings Object
    $containerBuilder->addDefinitions([
        SettingsInterface::class => function () {
            return new Settings([
                'displayErrorDetails' => true, // Should be set to false in production
                'logError'            => false,
                'logErrorDetails'     => false,
                'logger' => [
                    'name' => 'ravs_api',
                    'path' => isset($_ENV['docker']) ? 'php://stdout' : __DIR__ . '/../logs/app.log',
                    'level' => Logger::DEBUG,
                ],
                'database' => [
                    'user' => 'root',
                    'password' => '',
                    'host' => '127.0.0.1',
                    'name' => 'ravs'
                ],
                'smtp' => [
                    'host' => 'smtp.gmail.com',
                    'port' => 587,
                    'username' => 'szymonrykala@gmail.com',
                    'password' => 'rolekskejt1214',
                    'mailerName' => 'Ravs system',
                    'debug' => 0
                ]
            ]);
        }
    ]);
};
