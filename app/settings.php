<?php

declare(strict_types=1);

use App\Application\Settings\Settings;
use App\Application\Settings\SettingsInterface;
use DI\ContainerBuilder;


function env(string $name, string|int|bool|null $default = NULL)
{
    $val = getenv($name);

    if ($val) return $val;
    elseif (!$val && $default !== NULL) return $default;

    throw new Exception("You must specify '$name' environment variable");
}


return function (ContainerBuilder $containerBuilder) {

    // Global Settings Object
    $containerBuilder->addDefinitions([
        SettingsInterface::class => function () {
            return new Settings([
                'displayErrorDetails' => boolval(env('DISPLAY_ERROR_DETAILS', false)), // Should be set to false in production
                'logError'            => boolval(env('LOG_ERROR_DETAILS', false)),
                'logErrorDetails'     => boolval(env('LOG_ERROR', false)),
                'logger' => [
                    'name' => 'ravs_api',
                    'path' => env('LOG_PATH', 'php://stdout'),
                    'level' => env('LOGGER_LEVEL', 'DEBUG'),
                ],
                'databaseUrl' => env('DATABASE_URL'),
                'token' => [
                    'secret' => env('TOKEN_SECRET'),
                    'expiry' => env('TOKEN_EXPIRY', "1"),
                    'encoding' => env('TOKEN_SIPHER_ALGORITHM', 'HS512'),
                ],
                'smtp' => [
                    'host' => env('SMTP_HOST'),
                    'port' => env('SMTP_PORT'),
                    'username' => env('SMTP_USER'),
                    'password' => env('SMTP_PASSWORD'),
                    'mailerName' => 'Rav System',
                    'debug' => (int) env('SMTP_DEBUG', 0)
                ],
                'cloudinary' => [
                    'cloudName' => env('CLOUDINARY_CLOUD_NAME'),
                    'secret' => env('CLOUDINARY_SECRET'),
                    'key' => env('CLOUDINARY_KEY')
                ]
            ]);
        }
    ]);
};
