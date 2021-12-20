<?php

declare(strict_types=1);

namespace App\Domain\Reservation;

use App\Domain\Model\Model;
use App\Domain\Room\Room;
use App\Domain\User\User;
use App\Utils\JsonDateTime;


final class Reservation extends Model
{
    public int $userId;
    public int $roomId;

    public function __construct(
        public int         $id,
        public string      $title,
        public string      $description,
        public Room         $room,
        public User         $user,
        public JsonDateTime    $plannedStart,
        public JsonDateTime    $plannedEnd,
        public ?JsonDateTime    $actualStart,
        public ?JsonDateTime    $actualEnd,
        public JsonDateTime    $created,
        public JsonDateTime    $updated
    ) {
        parent::__construct($id, $created, $updated);

        $this->roomId = $room->id;
        $this->userId = $user->id;
    }

    /**
     * @return bool
     */
    public function notStarted(): bool
    {
        return !$this->actualStart && !$this->actualEnd;
    }

    /**
     * @return bool
     */
    public function hasStarted(): bool
    {
        return $this->actualStart && !$this->actualEnd;
    }

    /**
     * @return bool
     */
    public function hasEnded(): bool
    {
        return $this->actualStart && $this->actualEnd;
    }

    /**
     * @return void
     * @throws ReservationAlreadyStartedException
     */
    public function start(): void
    {
        if (!$this->actualStart && !$this->actualEnd) {
            $this->actualStart = new JsonDateTime('now');
        } else
            throw new ReservationAlreadyStartedException();
    }

    /**
     * @return void
     * @throws ReservationAlreadyEndedException
     */
    public function end(): void
    {
        if ($this->actualStart && !$this->actualEnd) {
            $this->actualEnd = new JsonDateTime('now');
        } else
            throw new ReservationAlreadyEndedException();
    }


    /**
     * @return array
     */
    public function jsonSerialize(): array
    {
        return array_merge(
            [
                'title' => $this->title,
                'description' => $this->description,
                'room' => $this->room ?? $this->roomId,
                'user' => $this->user ?? $this->userId,
                'plannedStart' => $this->plannedStart,
                'plannedEnd' => $this->plannedEnd,
                'actualStart' => $this->actualStart,
                'actualEnd' => $this->actualEnd
            ],
            parent::jsonSerialize()
        );
    }
}
