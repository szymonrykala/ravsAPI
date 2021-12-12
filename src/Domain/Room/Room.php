<?php

declare(strict_types=1);

namespace App\Domain\Room;

use App\Domain\Model\Model;
use App\Domain\Image\Image;
use App\Domain\Building\Building;
use App\Domain\Exception\DomainConflictException;
use App\Utils\JsonDateTime;



final class Room extends Model
{
    public function __construct(
        public int     $id,
        public string  $name,
        public Image   $image,
        public ?Building $building,
        public ?string  $rfid,
        public string  $roomType,
        public int     $seatsCount,
        public int     $floor,
        public bool    $blocked,
        public bool    $occupied,
        public JsonDateTime  $created,
        public JsonDateTime  $updated,
        public int     $imageId,
        public int     $buildingId
    ) {
        parent::__construct($id, $created, $updated);
    }

    /**
     * {@inheritDoc}
     * @throws DomainConflictException
     */
    protected function validateCallback(): void
    {
        if ($this->blocked === FALSE && empty($this->rfid)) {
            throw new DomainConflictException("Room without 'NFCTag' cannot be unblocked");
        }
    }

    /**
     * Validates if provided rfid key is correct
     * @throws RfidKeyNotValidException
     */
    public function valiadateRfidKey(string $key): void
    {
        // if key is assigned and is different
        if ($this->rfid && $key !== $this->rfid)
            throw new RfidKeyNotValidException();
    }

    /**
     * Mark room as occupied - is under pending reservation
     * @throws RoomAlreadyOccupiedException
     */
    public function occupy(): void
    {
        if ($this->occupied)
            throw new RoomAlreadyOccupiedException();

        $this->occupied = TRUE;
    }

    /**
     * Marks room as free
     * @throws RoomAlreadyEmptyException
     */
    public function release(): void
    {
        if ($this->occupied === FALSE)
            throw new RoomAlreadyEmptyException();

        $this->occupied = FALSE;
    }

    /**
     * {@inheritDoc}
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
                "RFIDTag" => $this->rfid
            ],
            parent::jsonSerialize()
        );
    }
}
