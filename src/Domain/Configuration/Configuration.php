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
    public int $maxImageSize;
    public int $defaultUserAccess;
    public int $reservationHistory; //days
    public int $requestHistory; // days
    public DateInterval $maxReservationTime; //minutes
    public DateInterval $minReservationTime; //minutes


    public function __construct(array $data)
    {
        foreach ($data as $var) {

            switch ($var['key']) {
                case 'BUILDING_IMAGE':
                    $this->buildingImage = (int) $var['value'];
                    break;
                case 'ROOM_IMAGE':
                    $this->roomImage = (int) $var['value'];
                    break;
                case 'USER_IMAGE':
                    $this->userImage = (int) $var['value'];
                    break;
                case 'MAX_IMAGE_SIZE':
                    $this->maxImageSize = (int) $var['value'];
                    break;
                case 'DEFAULT_USER_ACCESS':
                    $this->defaultUserAccess = (int) $var['value'];
                    break;
                case 'MAX_RESERVATION_TIME':
                    $this->maxReservationTime = new DateInterval('PT' . $var['value'] . 'M');
                    break;
                case 'MIN_RESERVATION_TIME':
                    $this->minReservationTime = new DateInterval('PT' . $var['value'] . 'M');
                    break;
                case 'RESERVATION_HISTORY':
                    $this->reservationHistory = (int) $var['value'];
                    break;                
                case 'REQUEST_HISTORY':
                    $this->requestHistory = (int) $var['value'];
                    break;
                default:
                    //nothing
                    break;
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function update(stdClass $form): void
    {
        foreach (['minReservationTime', 'maxReservationTime'] as  $field) {
            isset($form->$field)
                && $form->$field = new DateInterval('PT' . $form->$field . 'M');
        }

        parent::update($form);
    }


    public function jsonSerialize(): array
    {
        return [
            'buildingImage' => $this->buildingImage,
            'roomImage' => $this->roomImage,
            'userImage' => $this->userImage,
            'maxImageSize' => $this->maxImageSize, //bytes
            'defaultUserAccess' => $this->defaultUserAccess,
            'maxReservationTime' => $this->maxReservationTime->i, // minutes
            'minReservationTime' => $this->minReservationTime->i, // minutes
            'reservationHistory' => $this->reservationHistory,
            'requestHistory' => $this->requestHistory,
        ];
    }
}
