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

function registerReservation(&$roomPath)
{
    $roomPath->group('/reservations', function (Group $reservations) {
        $reservations->get('', Reservation\ListReservations::class);
        $reservations->post('', Reservation\CreateReservation::class);

        $reservations->group('/{reservation_id}', function (Group $reservation) {
            $reservation->get('', Reservation\ViewReservation::class);
        });
    });
}

function registerRooms(&$buildingPath)
{

    $buildingPath->group('/rooms', function (Group $rooms) {
        $rooms->get('', Room\ListRoomsAction::class);
        $rooms->post('', Room\CreateRoomAction::class);

        $rooms->group('/{room_id}', function (Group $one) {
            $one->get('', Room\ViewRoomAction::class);
            $one->patch('', Room\UpdateRoomAction::class);

            $one->patch('/keys', Key\AssignKey::class);

            registerReservation($one);
        });
    });
}

function registerBuildings(&$addressPath)
{
    $addressPath->group('/buildings', function (Group $buildings) {
        $buildings->get('', Building\ListBuildingsAction::class);
        $buildings->post('', Building\CreateBuildingAction::class);

        $buildings->group('/{building_id}', function (Group $building) {
            $building->get('', Building\ViewBuildingAction::class);
            $building->patch('', Building\UpdateBuildingAction::class);
            $building->delete('', Building\DeleteBuildingAction::class);

            addReservationGets($building);
            registerRooms($building);
        });
    });
}

function registerAddresses(&$authRoot)
{
    $authRoot->group('/addresses', function (Group $addresses) {
        $addresses->get('', Address\ListAllAddressesAction::class);
        $addresses->post('', Address\CreateAddressAction::class);

        $addresses->group('/{address_id}', function (Group $address) {
            $address->get('', Address\ViewAddressAction::class);
            $address->patch('', Address\UpdateAddressAction::class);
            $address->delete('', Address\DeleteAddressAction::class);

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

            $auth->get('/{subject:.*}/requests', RequestActions\ListRequestsAction::class);
            $auth->delete('/requests', RequestActions\DeleteRequestsAction::class);

            $auth->group('/configurations', function (Group $configs) {
                $configs->get('', Configuration\ViewConfiguration::class);
                $configs->patch('', Configuration\UpdateConfiguration::class);
            });

            $auth->group('/images', function (Group $images) {
                $images->post('', Image\UploadImageAction::class);
                $images->get('', Image\ListAllImagesAction::class);

                $images->group('/{id:[0-9]+}', function (Group $image) {
                    $image->get('', Image\ViewImageAction::class);
                    $image->delete('', Image\DeleteImageAction::class);
                });
            });

            $auth->group('/users', function (Group $users) {
                $users->get('/me', User\ViewCurrentUser::class);
                $users->get('', User\ListAllUsers::class);
                $users->post('', User\RegisterUser::class);

                $users->group('/{user_id:[0-9]+}', function (Group $user) {
                    $user->get('', User\ViewUser::class);
                    $user->patch('', User\UpdateUser::class);
                    $user->patch('/access', User\UpdateUserAccess::class);
                    $user->delete('', User\DeleteUser::class);

                    addReservationGets($user);

                    // $one->get('/report', User\GenerateUserReport::class);
                });
            });

            $auth->group('/accesses', function (Group $accesses) {
                $accesses->get('', Access\ListAllAccessesAction::class);
                $accesses->post('', Access\CreateAccessAction::class);

                $accesses->group('/{id:[0-9]+}', function (Group $user) {
                    $user->get('', Access\ViewAccessAction::class);
                    $user->patch('', Access\UpdateAccessAction::class);
                    $user->delete('', Access\DeleteAccessAction::class);
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
