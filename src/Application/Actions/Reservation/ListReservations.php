<?php

declare(strict_types=1);

namespace App\Application\Actions\Reservation;

use App\Utils\JsonDateTime;
use Psr\Http\Message\ResponseInterface as Response;


final class ListReservations extends ReservationAction
{
    /**
     * {@inheritDoc}
     */
    protected function action(): Response
    {
        $pagination = $this->preparePagination();

        $this->resolveURIParams();

        $searchString = $this->resolveQueryArg($this::SEARCH_STRING, FALSE);
        $dateString = $this->resolveQueryArg($this::FROM_DATE, FALSE);


        $searchString && $this->reservations->search($searchString);
        $dateString && $this->reservations->fromDate(new JsonDateTime($dateString));


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
        $buildingId = $this->resolveArg($this::BUILDING_ID, FALSE);
        $addressId = $this->resolveArg($this::ADDRESS_ID, FALSE);

        if ($buildingId && $addressId) {
            $this->reservations
                ->whereAddressAndBuilding((int)$addressId, (int)$buildingId);
        } else {
            $addressId && $this->reservations->whereAddressId((int)$addressId);
            $buildingId && $this->reservations->whereBuildingId((int)$buildingId);
        }

        $roomId = $this->resolveArg($this::ROOM_ID, FALSE);
        $userId = $this->resolveArg($this::USER_ID, FALSE);

        $roomId && $this->reservations->forRoom((int)$roomId);
        $userId && $this->reservations->forUser((int)$userId);
    }
}
