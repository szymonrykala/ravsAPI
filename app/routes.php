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
    Request as RequestActions
};


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

            $auth->group('/images', function (Group $group) {
                $group->post('', Image\UploadImageAction::class);
                $group->get('', Image\ListAllImagesAction::class);

                $group->group('/{id:[0-9]+}', function (Group $one) {
                    $one->get('', Image\ViewImageAction::class);
                    $one->delete('', Image\DeleteImageAction::class);
                });
            });

            $auth->group('/users', function (Group $group) {
                $group->get('/me', User\ViewCurrentUserAction::class);
                $group->get('', User\ListAllUsersAction::class);
                $group->post('', User\RegisterUserAction::class);

                $group->group('/{userId:[0-9]+}', function (Group $one) {
                    $one->get('', User\ViewUserAction::class);
                    $one->patch('', User\UpdateUserAction::class);
                    $one->patch('/access', User\UpdateUserAccessAction::class);
                    $one->delete('', User\DeleteUserAction::class);

                    // $one->get('/report', User\GenerateUserReportAction::class);
                });
            });

            $auth->group('/accesses', function (Group $group) {
                $group->get('', Access\ListAllAccessesAction::class);
                $group->post('', Access\CreateAccessAction::class);

                $group->group('/{id:[0-9]+}', function (Group $one) {
                    $one->get('', Access\ViewAccessAction::class);
                    $one->patch('', Access\UpdateAccessAction::class);
                    $one->delete('', Access\DeleteAccessAction::class);
                });
            });

            $auth->get('/requests', RequestActions\ListRequestsAction::class);
            $auth->delete('/requests', RequestActions\DeleteRequestsAction::class);
            $auth->group('/{subject:[a-z]+}', function (Group $group) {
                $group->get('/requests', RequestActions\ListRequestsAction::class);
                $group->get('/{subjectId:[0-9]+}/requests', RequestActions\ListRequestsAction::class);
            });


            $auth->group('/addresses', function (Group $group) {
                $group->get('', Address\ListAllAddressesAction::class);
                $group->post('', Address\CreateAddressAction::class);

                $group->group('/{address_id}', function (Group $one) {
                    $one->get('', Address\ViewAddressAction::class);
                    $one->patch('', Address\UpdateAddressAction::class);
                    $one->delete('', Address\DeleteAddressAction::class);

                    $one->group('/buildings', function (Group $group) {
                        $group->get('', Building\ListBuildingsAction::class);
                        $group->post('', Building\CreateBuildingAction::class);

                        $group->group('/{building_id}', function (Group $one) {
                            $one->get('', Building\ViewBuildingAction::class);
                            $one->patch('', Building\UpdateBuildingAction::class);
                            $one->delete('', Building\DeleteBuildingAction::class);

                            $one->group('/rooms', function (Group $group) {
                                $group->get('', Room\ListRoomsAction::class);
                                $group->post('', Room\CreateRoomAction::class);

                                $group->group('/{room_id}', function (Group $one) {
                                    $one->get('', Room\ViewRoomAction::class);
                                    $one->patch('', Room\UpdateRoomAction::class);
                                });
                            });
                        });
                    });
                });
            });
        });
    });
};
