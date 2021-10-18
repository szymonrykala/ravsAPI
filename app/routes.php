<?php

declare(strict_types=1);


use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\App;
use Slim\Interfaces\RouteCollectorProxyInterface as Group;

use Psr\Http\Server\{
    RequestHandlerInterface as RequestHandler
};

use App\Application\Actions\{
    Image,
    Access,
    User,
    Address,
    Building,
    Room,
    Request as RequestActions,
    Reservation,
    Configuration,
    Key,
    Stats
};
use App\Application\Middleware\Auth\AuthorizationMiddleware;
use App\Application\Middleware\BodyParsingMiddleware;
use App\Application\Middleware\RequestLoggingMiddleware;
use App\Application\Middleware\SchemaValidationMiddleware;
use App\Application\Middleware\SessionMiddleware;
use App\Application\Middleware\UserActivityMiddleware;

function addReservationGets(&$resourcePath)
{
    $resourcePath->group('/reservations', function (Group $reservations) {
        $reservations->get('', Reservation\ListReservations::class);

        $reservations->group('/{reservation_id:[0-9]+}', function (Group $reservation) {
            $reservation->get('', Reservation\ViewReservation::class);
        });
    });
}


function registerRooms(&$buildingPath)
{

    $buildingPath->group('/rooms', function (Group $rooms) {
        $rooms->get('', Room\ListRooms::class);
        $rooms->get('/stats', Stats\ViewRoomStats::class);

        $rooms->post('', Room\CreateRoom::class);

        $rooms->group('/{room_id:[0-9]+}', function (Group $room) {
            $room->get('', Room\ViewRoom::class);
            $room->patch('', Room\UpdateRoom::class);

            $room->get('/stats', Stats\ViewRoomStats::class);

            $room->get('/keys', Key\ViewKey::class);
            $room->patch('/keys', Key\AssignKey::class);

            // registerReservation($room);
            $room->post('/reservations', Reservation\CreateReservation::class);
            addReservationGets($room);
        });
    });
}

function registerBuildings(&$addressPath)
{
    $addressPath->group('/buildings', function (Group $buildings) {
        $buildings->get('', Building\ListBuildings::class);
        $buildings->get('/stats', Stats\ViewBuildingStats::class);

        $buildings->post('', Building\CreateBuilding::class);

        $buildings->group('/{building_id:[0-9]+}', function (Group $building) {
            $building->get('', Building\ViewBuilding::class);
            $building->get('/stats', Stats\ViewBuildingStats::class);

            $building->patch('', Building\UpdateBuilding::class);
            $building->delete('', Building\DeleteBuilding::class);

            addReservationGets($building);
            registerRooms($building);
        });
    });
}

function registerAddresses(&$authRoot)
{
    $authRoot->group('/addresses', function (Group $addresses) {
        $addresses->get('', Address\ListAllAddresses::class);
        $addresses->get('/resources', Address\ViewResourcesMap::class);
        $addresses->post('', Address\CreateAddress::class);

        $addresses->group('/{address_id:[0-9]+}', function (Group $address) {
            $address->get('', Address\ViewAddress::class);
            $address->patch('', Address\UpdateAddress::class);
            $address->delete('', Address\DeleteAddress::class);

            addReservationGets($address);
            registerBuildings($address);
        });
    });
}


return function (App $app) {
    $app->options('/{routes:.*}', function (Request $request, Response $response) {
        // CORS Pre-Flight OPTIONS Request Handler
        return $response;
    });


    $app->group('/v1', function (Group $v1) {

        $v1->group('/users', function (Group $unAuth) {
            $unAuth->post('', User\RegisterUser::class);

            $unAuth->post('/auth', User\AuthenticateUser::class);
            $unAuth->post('/key', User\GenerateUserKey::class);
            $unAuth->patch('/activate', User\ActivateUser::class);
            $unAuth->patch('/password', User\ChangeUserPassword::class);
        })->add(RequestLoggingMiddleware::class);

        $v1->group('', function (Group $protected) {

            $protected->get('/{request_subject:.*}/requests', RequestActions\ListRequests::class);
            $protected->get('/requests/stats', Stats\ViewRequestStats::class);

            $protected->group('/configurations', function (Group $configs) {
                $configs->get('', Configuration\ViewConfiguration::class);
                $configs->patch('', Configuration\UpdateConfiguration::class);
            });

            $protected->group('/users', function (Group $users) {
                $users->get('/me', User\ViewCurrentUser::class);
                $users->get('', User\ListAllUsers::class);
                $users->get('/stats', Stats\ViewUserStats::class);

                $users->group('/{user_id:[0-9]+}', function (Group $user) {
                    $user->get('', User\ViewUser::class);
                    $user->get('/stats', Stats\ViewUserStats::class);
                    $user->patch('', User\UpdateUser::class);
                    $user->patch('/access', User\UpdateUserAccess::class);
                    $user->delete('', User\DeleteUser::class);

                    addReservationGets($user);
                });
            });

            $protected->group('/accesses', function (Group $accesses) {
                $accesses->get('', Access\ListAllAccesses::class);
                $accesses->post('', Access\CreateAccess::class);

                $accesses->group('/{access_id:[0-9]+}', function (Group $user) {
                    $user->get('', Access\ViewAccess::class);
                    $user->patch('', Access\UpdateAccess::class);
                    $user->delete('', Access\DeleteAccess::class);
                });
            });

            addReservationGets($protected);
            $protected->group('/reservations', function (Group $reservations) {
                $reservations->post('', Reservation\CreateReservation::class);

                $reservations->group('/{reservation_id:[0-9]+}', function (Group $reservation) {
                    $reservation->patch('', Reservation\UpdateReservation::class);
                    $reservation->delete('', Reservation\DeleteReservation::class);

                    $reservation->patch('/keys', Key\HandOverKey::class);
                });
            });


            // appends /addresses; /buildings; /rooms; /reservations
            registerAddresses($protected);

            $protected->group('/images', function (Group $images) {
                $images->post('', Image\UploadImage::class);

                $images->group('/{image_id:[0-9]+}', function (Group $image) {
                    $image->get('', Image\ViewImage::class);
                    $image->delete('', Image\DeleteImage::class);
                });
            });
        })->add(AuthorizationMiddleware::class)
            ->add(RequestLoggingMiddleware::class)
            ->add(UserActivityMiddleware::class)
            ->add(SessionMiddleware::class);

    })->add(SchemaValidationMiddleware::class)
        ->add(BodyParsingMiddleware::class);
};
