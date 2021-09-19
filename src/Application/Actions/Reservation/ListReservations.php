<?php

declare(strict_types=1);

namespace App\Application\Actions\Reservation;

use App\Utils\JsonDateTime;
use Psr\Http\Message\ResponseInterface as Response;
use Slim\Exception\HttpBadRequestException;

class ListReservations extends ReservationAction
{
    /**
     * {@inheritdoc}
     */
    protected function action(): Response
    {
        $pagination = $this->preparePagination();

        $this->resolveURIParams();

        // $this->reservations->where($params);

        $this->resolveFromDateParam();
        $this->resolveSearchParam();


        $list = $this->reservations
            ->orderBy('planned_start', 'ASC')
            ->setPagination($pagination)
            ->all();

        return $this->respondWithData($list);
    }

    /**
     * Resolves defined URI arguments to read reservations for specific resource
     */
    private function resolveURIParams(): void
    {
        $buildingId = $this->resolveArg('building_id', FALSE);
        $addressId = $this->resolveArg('address_id', FALSE);

        if ($buildingId && $addressId) {
            $this->reservations
                ->whereAddressAndBuilding((int)$addressId, (int)$buildingId);
        } else {
            $addressId && $this->reservations->whereAddressId((int)$addressId);
            $buildingId && $this->reservations->whereBuildingId((int)$buildingId);
        }

        $roomId = $this->resolveArg('room_id', FALSE);
        $userId = $this->resolveArg('user_id', FALSE);

        $roomId && $this->reservations->forRoom((int)$roomId);
        $userId && $this->reservations->forUser((int)$userId);
    }

    /**
     * @return void
     */
    private function resolveSearchParam(): void
    {
        $searchString = $this->resolveQueryArg('search', FALSE);
        if ($searchString) {
            $this->reservations->like($searchString);
        }
    }

    /**
     * @return void
     */
    private function resolveRoomIdParam(): void
    {
        if (isset($this->args['room_id']))
            $this->reservations->forRoom((int)$this->resolveArg('room_id'));
    }

    /**
     * @return void
     */
    private function resolveUserIdParam(): void
    {
        if (isset($this->args['user_id']))
            $this->reservations->forUser((int) $this->resolveArg('user_id'));
    }

    /**
     * @return void
     */
    private function resolveFromDateParam(): void
    {
        $dateString = $this->resolveQueryArg('fromDate', FALSE);
        if ($dateString) {
            try {
                $date = new JsonDateTime($dateString);
            } catch (\Exception $e) {
                throw new HttpBadRequestException($this->request, $e->getMessage());
            }
            $this->reservations->fromDate($date);
        }
    }
}
