<?php

declare(strict_types=1);

use App\Application\Settings\Settings;
use App\Application\Settings\SettingsInterface;
use DI\ContainerBuilder;


function _env(string $name, string|int|bool|null $default = NULL)
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
                'displayErrorDetails' => boolval(_env('DISPLAY_ERROR_DETAILS', false)), // Should be set to false in production
                'logError'            => boolval(_env('LOG_ERROR_DETAILS', false)),
                'logErrorDetails'     => boolval(_env('LOG_ERROR', false)),
                'logger' => [
                    'name' => 'ravs_api',
                    'path' => _env('LOG_PATH', 'php://stdout'),
                    'level' => _env('LOGGER_LEVEL', 'DEBUG'),
                ],
                'databaseUrl' => _env('DATABASE_URL'),
                'token' => [
                    'secret' => _env('TOKEN_SECRET'),
                    'expiry' => _env('TOKEN_EXPIRY', "1"),
                    'encoding' => _env('TOKEN_SIPHER_ALGORITHM', 'HS512'),
                ],
                'smtp' => [
                    'host' => _env('SMTP_HOST'),
                    'port' => _env('SMTP_PORT'),
                    'username' => _env('SMTP_USER'),
                    'password' => _env('SMTP_PASSWORD'),
                    'mailerName' => 'Rav System',
                    'debug' => (int) _env('SMTP_DEBUG', 0)
                ],
                'cloudinary' => [
                    'cloudName' => _env('CLOUDINARY_CLOUD_NAME'),
                    'secret' => _env('CLOUDINARY_SECRET'),
                    'key' => _env('CLOUDINARY_KEY')
                ]
            ]);
        }
    ]);
};
