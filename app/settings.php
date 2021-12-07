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
                'displayErrorDetails' => boolval(getenv('DISPLAY_ERROR_DETAILS') ?? false), // Should be set to false in production
                'logError'            => boolval(getenv('LOG_ERROR_DETAILS') ?? false),
                'logErrorDetails'     => boolval(getenv('LOG_ERROR') ?? false),
                'logger' => [
                    'name' => 'ravs_api',
                    'path' => getenv('LOG_PATH') ?? 'php://stdout',
                    'level' => getenv('LOGGER_LEVEL'),
                ],
                'databaseUrl' => getenv('DATABASE_URL'),
                'token' => [
                    'secret' => getenv('TOKEN_SECRET'),
                    'expiry' => getenv('TOKEN_EXPIRY') ?? "1",
                    'encoding' => getenv('TOKEN_SIPHER_ALGORITHM') ?? 'HS512',
                ],
                'smtp' => [
                    'host' => getenv('SMTP_HOST'),
                    'port' => getenv('SMTP_PORT'),
                    'username' => getenv('SMTP_USER'),
                    'password' => getenv('SMTP_PASSWORD'),
                    'mailerName' => 'Rav System',
                    'debug' => (int) getenv('SMTP_DEBUG') ?? 0
                ],
                'cloudinary' => [
                    'cloudName' => getenv('CLOUDINARY_CLOUD_NAME'),
                    'secret' => getenv('CLOUDINARY_SECRET'),
                    'key' => getenv('CLOUDINARY_KEY')
                ]
            ]);
        }
    ]);
};
