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
    Reservation
};



function addRequestsPath(&$subjectGroup)
{
    $subjectGroup->get('/requests', RequestActions\ListRequestsAction::class);
    $subjectGroup->get('/{subjectId:[0-9]+}/requests', RequestActions\ListRequestsAction::class);
}

function registerReservation(&$roomPath)
{
    $roomPath->group('/reservations', function (Group $reservations) {
        $reservations->get('', Reservation\ListReservationsAction::class);
        $reservations->post('', Reservation\CreateReservationAction::class);

        addRequestsPath($reservations);

        $reservations->group('/{reservation_id}', function (Group $reservation) {
            $reservation->get('', Reservation\ViewReservationAction::class);
            $reservation->patch('', Reservation\UpdateReservationAction::class);
        });
    });
}

function registerRooms(&$buildingPath)
{

    $buildingPath->group('/rooms', function (Group $rooms) {
        $rooms->get('', Room\ListRoomsAction::class);
        $rooms->post('', Room\CreateRoomAction::class);

        addRequestsPath($rooms);

        $rooms->group('/{room_id}', function (Group $one) {
            $one->get('', Room\ViewRoomAction::class);
            $one->patch('', Room\UpdateRoomAction::class);

            registerReservation($one);
        });
    });
}

function registerBuildings(&$addressPath)
{
    $addressPath->group('/buildings', function (Group $buildings) {
        $buildings->get('', Building\ListBuildingsAction::class);
        $buildings->post('', Building\CreateBuildingAction::class);

        addRequestsPath($buildings);

        $buildings->group('/{building_id}', function (Group $building) {
            $building->get('', Building\ViewBuildingAction::class);
            $building->patch('', Building\UpdateBuildingAction::class);
            $building->delete('', Building\DeleteBuildingAction::class);

            registerRooms($building);
        });
    });
}

function registerAddresses(&$authRoot)
{
    $authRoot->group('/addresses', function (Group $addresses) {
        $addresses->get('', Address\ListAllAddressesAction::class);
        $addresses->post('', Address\CreateAddressAction::class);

        addRequestsPath($addresses);

        $addresses->group('/{address_id}', function (Group $address) {
            $address->get('', Address\ViewAddressAction::class);
            $address->patch('', Address\UpdateAddressAction::class);
            $address->delete('', Address\DeleteAddressAction::class);

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
            $unAuth->post('/auth', User\AuthenticateUserAction::class);
            $unAuth->post('/key', User\GenerateUserKeyAction::class);
            $unAuth->patch('/activate', User\ActivateUserAction::class);
            $unAuth->patch('/password', User\ChangeUserPasswordAction::class);
        });
        // ------------ END ---------------

        $v1->group('', function (Group $auth) {

            $auth->group('/images', function (Group $images) {
                $images->post('', Image\UploadImageAction::class);
                $images->get('', Image\ListAllImagesAction::class);

                addRequestsPath($images);

                $images->group('/{id:[0-9]+}', function (Group $image) {
                    $image->get('', Image\ViewImageAction::class);
                    $image->delete('', Image\DeleteImageAction::class);
                });
            });

            $auth->group('/users', function (Group $users) {
                $users->get('/me', User\ViewCurrentUserAction::class);
                $users->get('', User\ListAllUsersAction::class);
                $users->post('', User\RegisterUserAction::class);

                addRequestsPath($users);

                $users->group('/{userId:[0-9]+}', function (Group $user) {
                    $user->get('', User\ViewUserAction::class);
                    $user->patch('', User\UpdateUserAction::class);
                    $user->patch('/access', User\UpdateUserAccessAction::class);
                    $user->delete('', User\DeleteUserAction::class);

                    // $one->get('/report', User\GenerateUserReportAction::class);
                });
            });

            $auth->group('/accesses', function (Group $accesses) {
                $accesses->get('', Access\ListAllAccessesAction::class);
                $accesses->post('', Access\CreateAccessAction::class);

                addRequestsPath($accesses);

                $accesses->group('/{id:[0-9]+}', function (Group $user) {
                    $user->get('', Access\ViewAccessAction::class);
                    $user->patch('', Access\UpdateAccessAction::class);
                    $user->delete('', Access\DeleteAccessAction::class);
                });
            });

            // appends /addresses; /buildings; /rooms; /reservations
            registerAddresses($auth);
        });
    });
};
