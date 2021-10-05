<?php

declare(strict_types=1);

use Slim\App;

use App\Application\Middleware\{
    SchemaValidationMiddleware,
    BodyParsingMiddleware,
    RequestLoggingMiddleware,
    SessionMiddleware,
    AuthorizationMiddleware
};

return function (App $app) {
    $app
    ->add(SchemaValidationMiddleware::class) // in 5 validate data schema
    ->add(RequestLoggingMiddleware::class) // in 4 log user request
    ->add(AuthorizationMiddleware::class) // in 3 authorize user
    ->add(SessionMiddleware::class)     // in 2 check user session
    ->add(BodyParsingMiddleware::class); // in 1 parse send data
};
