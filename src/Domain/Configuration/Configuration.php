<?php

declare(strict_types=1);

namespace App\Domain\Configuration;

use App\Domain\Model\Model;
use DateInterval;
use stdClass;

final class Configuration extends Model
{
    public int $buildingImage;
    public int $roomImage;
    public int $userImage;
    public int $maxImageSize; // Bytes
    public int $defaultUserAccess;
    public int $reservationHistory; //days
    public int $requestHistory; // days
    public DateInterval $maxReservationTime; //minutes
    public DateInterval $minReservationTime; //minutes


    public function __construct(array $data)
    {
        foreach ($data as $var) {

            $field = match($var['key']){
                'MAX_IMAGE_SIZE' => 'maxImageSize',
                'DEFAULT_USER_ACCESS' => 'defaultUserAccess',
                'MAX_RESERVATION_TIME' => 'maxReservationTime',
                'MIN_RESERVATION_TIME' => 'minReservationTime',
                'RESERVATION_HISTORY' => 'reservationHistory',
                'REQUEST_HISTORY' => 'requestHistory'
            };

            if(str_contains($var['key'],'RESERVATION_TIME')){
                $this->$field = new DateInterval('PT' . $var['value'] . 'M');
            }else{
                $this->$field = (int) $var['value'];
            }
        }
    }

    /**
     * {@inheritDoc}
     */
    public function update(stdClass $form): void
    {
        foreach (['minReservationTime', 'maxReservationTime'] as  $field) {
            isset($form->$field)
                && $form->$field = new DateInterval('PT' . $form->$field . 'M');
        }

        parent::update($form);
    }

    /**
     * {@inheritDoc}
     */
    public function jsonSerialize(): array
    {
        return [
            'maxImageSize' => $this->maxImageSize, //bytes
            'defaultUserAccess' => $this->defaultUserAccess,
            'maxReservationTime' => $this->maxReservationTime->i, // minutes
            'minReservationTime' => $this->minReservationTime->i, // minutes
            'reservationHistory' => $this->reservationHistory,
            'requestHistory' => $this->requestHistory,
        ];
    }
}
