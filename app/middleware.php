<?php

declare(strict_types=1);

use Slim\App;

use App\Application\Middleware\{
    SchemaValidationMiddleware,
    BodyParsingMiddleware,
    RequestLoggingMiddleware,
    SessionMiddleware,
    ConfigurationLoadingMiddleware,
    AuthorizationMiddleware
};

return function (App $app) {
    $app
    ->add(SchemaValidationMiddleware::class)
    ->add(ConfigurationLoadingMiddleware::class)
    ->add(RequestLoggingMiddleware::class)
    ->add(SessionMiddleware::class)
    ->add(BodyParsingMiddleware::class);
};
