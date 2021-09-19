<?php

declare(strict_types=1);

namespace App\Domain\Room;

use App\Domain\Model\Model;
use App\Domain\Image\Image;
use App\Domain\Building\Building;
use App\Domain\Exception\DomainConflictException;
use App\Utils\JsonDateTime;



class Room extends Model
{
    public string $name;
    public Image $image;
    public ?Building $building;
    public string $rfid;
    public string $roomType;
    public int $seatsCount;
    public int $floor;
    public bool $blocked;
    public bool $occupied;

    public int $imageId;
    public int $buildingId;


    public function __construct(
        int     $id,
        string  $name,
        Image   $image,
        ?Building $building,
        string  $rfid,
        string  $roomType,
        int     $seatsCount,
        int     $floor,
        bool    $blocked,
        bool    $occupied,
        JsonDateTime  $created,
        JsonDateTime  $updated,
        int     $imageId,
        int     $buildingId
    ) {
        parent::__construct($id, $created, $updated);

        $this->name = $name;
        $this->image = $image;
        $this->building = $building;
        $this->rfid = $rfid;
        $this->roomType = $roomType;
        $this->seatsCount = $seatsCount;
        $this->floor = $floor;
        $this->blocked = $blocked;
        $this->occupied = $occupied;

        $this->buildingId = $buildingId;
        $this->imageId = $imageId;
    }

    /**
     * {@inheritdoc}
     * @throws DomainConflictException
     */
    protected function validateCallback(): void
    {
        if ($this->blocked === FALSE && empty($this->rfid)) {
            throw new DomainConflictException("Room without 'NFCTag' cannot be unblocked");
        }
    }

    /**
     * @param string key
     * @return void
     * @throws RfidKeyNotValidException
     */
    public function valiadateRfidKey(string $key): void
    {
        if ($key !== $this->rfid)
            throw new RfidKeyNotValidException();
    }

    /**
     * @return void
     * @throws RoomAlreadyOccupiedException
     */
    public function occupy(): void
    {
        if ($this->occupied)
            throw new RoomAlreadyOccupiedException();

        $this->occupied = TRUE;
    }

    /**
     * @return void
     * @throws RoomAlreadyEmptyException
     */
    public function release(): void
    {
        if ($this->occupied === FALSE)
            throw new RoomAlreadyEmptyException();

        $this->occupied = FALSE;
    }

    /**
     * @return array
     */
    public function jsonSerialize(): array
    {
        return array_merge(
            [
                "name" => $this->name,
                "image" => $this->image,
                "building" => $this->building ?? $this->buildingId,
                "roomType" => $this->roomType,
                "seatsCount" => $this->seatsCount,
                "floor" => $this->floor,
                "blocked" => $this->blocked,
                "occupied" => $this->occupied,
                "hasNFCTag" => (bool) $this->rfid
            ],
            parent::jsonSerialize()
        );
    }
}
