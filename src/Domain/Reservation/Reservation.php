<?php

declare(strict_types=1);

namespace App\Domain\Reservation;

use App\Domain\Model\Model;
use App\Domain\Room\Room;
use App\Domain\User\User;
use App\Utils\JsonDateTime;
use stdClass;

class Reservation extends Model
{
    public string      $title;
    public string      $description;
    public Room        $room;
    public User        $user;
    public JsonDateTime    $plannedStart;
    public JsonDateTime    $plannedEnd;
    public ?JsonDateTime    $actualStart;
    public ?JsonDateTime    $actualEnd;

    public int $userId;
    public int $roomId;


    public function __construct(
        int         $id,
        string      $title,
        string      $description,
        Room         $room,
        User         $user,
        JsonDateTime    $planned_start,
        JsonDateTime    $planned_end,
        ?JsonDateTime    $actual_start,
        ?JsonDateTime    $actual_end,
        JsonDateTime    $created,
        JsonDateTime    $updated
    ) {
        parent::__construct($id, $created, $updated);

        $this->title = $title;
        $this->description = $description;
        $this->room = $room;
        $this->user = $user;
        $this->plannedStart = $planned_start;
        $this->plannedEnd = $planned_end;
        $this->actualStart = $actual_start;
        $this->actualEnd = $actual_end;

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
            $this->actualStart = new JsonDateTime();
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
            $this->actualEnd = new JsonDateTime();
        } else
            throw new ReservationAlreadyEndedException();
    }


    /**
     * {@inheritdoc}
     */
    protected function validateCallback(): void
    {
        // validation details
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
