<?php

declare(strict_types=1);

use Slim\App;

use App\Application\Middleware\{
    SchemaValidationMiddleware,
    BodyParsingMiddleware,
    RequestLoggingMiddleware,
    SessionMiddleware,
    AuthorizationMiddleware,
    UserActivityMiddleware,
};

return function (App $app) {
    $app
    ->add(SchemaValidationMiddleware::class) // in 6 validate data schema
    ->add(RequestLoggingMiddleware::class) // in 5 log user request
    ->add(AuthorizationMiddleware::class) // in 4 authorize user
    ->add(UserActivityMiddleware::class) // in 3 register activity time of the user
    ->add(SessionMiddleware::class)     // in 2 check user session
    ->add(BodyParsingMiddleware::class); // in 1 parse send data
};
