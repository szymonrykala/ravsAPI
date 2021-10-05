<?php

declare(strict_types=1);


use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\App;
use Slim\Interfaces\RouteCollectorProxyInterface as Group;


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


function addReservationGets(&$resourcePath)
{
    $resourcePath->group('/reservations', function (Group $reservations) {
        $reservations->get('', Reservation\ListReservations::class);

        $reservations->group('/{reservation_id}', function (Group $reservation) {
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

        $rooms->group('/{room_id}', function (Group $room) {
            $room->get('', Room\ViewRoom::class);
            $room->patch('', Room\UpdateRoom::class);
            
            $room->get('/stats', Stats\ViewRoomStats::class);
            
            $room->get('/keys', Key\ViewKey::class);
            $room->patch('/keys', Key\AssignKey::class);

            // registerReservation($room);
            $room->post('/reservations', Reservation\CreateReservation::class);
            addReservationGets($room);
            registerImages($room);
        });
    });
}

function registerBuildings(&$addressPath)
{
    $addressPath->group('/buildings', function (Group $buildings) {
        $buildings->get('', Building\ListBuildings::class);
        $buildings->get('/stats', Stats\ViewBuildingStats::class);

        $buildings->post('', Building\CreateBuilding::class);

        $buildings->group('/{building_id}', function (Group $building) {
            $building->get('', Building\ViewBuilding::class);
            $building->get('/stats', Stats\ViewBuildingStats::class);

            $building->patch('', Building\UpdateBuilding::class);
            $building->delete('', Building\DeleteBuilding::class);

            addReservationGets($building);
            registerRooms($building);
            registerImages($building);
        });
    });
}

function registerAddresses(&$authRoot)
{
    $authRoot->group('/addresses', function (Group $addresses) {
        $addresses->get('', Address\ListAllAddresses::class);
        $addresses->get('/resources', Address\ViewResourcesMap::class);
        $addresses->post('', Address\CreateAddress::class);

        $addresses->group('/{address_id}', function (Group $address) {
            $address->get('', Address\ViewAddress::class);
            $address->patch('', Address\UpdateAddress::class);
            $address->delete('', Address\DeleteAddress::class);

            addReservationGets($address);
            registerBuildings($address);
        });
    });
}

function registerImages($resource)
{
    $resource->group('/images', function (Group $images) {
        $images->post('', Image\UploadImage::class);
        $images->get('', Image\ListAllImages::class);

        $images->group('/{image_id:[0-9]+}', function (Group $image) {
            $image->get('', Image\ViewImage::class);
            $image->delete('', Image\DeleteImage::class);
        });
    });
}


return function (App $app) {
    $app->options('/{routes:.*}', function (Request $request, Response $response) {
        // CORS Pre-Flight OPTIONS Request Handler
        return $response;
    });

    $app->group('', function (Group $v1) {

        // ------ WHITE LIST ENDPOINTS --------
        $v1->group('/users', function (Group $unAuth) {
            $unAuth->post('/auth', User\AuthenticateUser::class);
            $unAuth->post('/key', User\GenerateUserKey::class);
            $unAuth->patch('/activate', User\ActivateUser::class);
            $unAuth->patch('/password', User\ChangeUserPassword::class);
        });
        // ------------ END ---------------

        $v1->group('', function (Group $auth) {

            $auth->get('/{request_subject:.*}/requests', RequestActions\ListRequests::class);
            $auth->get('/requests/stats', Stats\ViewRequestStats::class);
            $auth->delete('/requests', RequestActions\DeleteRequests::class);

            $auth->group('/configurations', function (Group $configs) {
                $configs->get('', Configuration\ViewConfiguration::class);
                $configs->patch('', Configuration\UpdateConfiguration::class);
            });

            $auth->group('/users', function (Group $users) {
                $users->get('/me', User\ViewCurrentUser::class);
                $users->get('', User\ListAllUsers::class);
                $users->get('/stats', Stats\ViewUserStats::class);

                $users->post('', User\RegisterUser::class);

                $users->group('/{user_id:[0-9]+}', function (Group $user) {
                    $user->get('', User\ViewUser::class);
                    $user->get('/stats', Stats\ViewUserStats::class);
                    $user->patch('', User\UpdateUser::class);
                    $user->patch('/access', User\UpdateUserAccess::class);
                    $user->delete('', User\DeleteUser::class);

                    addReservationGets($user);
                    registerImages($user);
                    // $one->get('/report', User\GenerateUserReport::class);
                });
            });

            $auth->group('/accesses', function (Group $accesses) {
                $accesses->get('', Access\ListAllAccesses::class);
                $accesses->post('', Access\CreateAccess::class);

                $accesses->group('/{access_id:[0-9]+}', function (Group $user) {
                    $user->get('', Access\ViewAccess::class);
                    $user->patch('', Access\UpdateAccess::class);
                    $user->delete('', Access\DeleteAccess::class);
                });
            });

            addReservationGets($auth);
            $auth->group('/reservations', function (Group $reservations) {
                $reservations->post('', Reservation\CreateReservation::class);

                $reservations->group('/{reservation_id}', function (Group $reservation) {
                    $reservation->patch('', Reservation\UpdateReservation::class);
                    $reservation->delete('', Reservation\DeleteReservation::class);

                    $reservation->patch('/keys', Key\HandOverKey::class);
                });
            });


            // appends /addresses; /buildings; /rooms; /reservations
            registerAddresses($auth);
        });
    });
};
