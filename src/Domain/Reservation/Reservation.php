<?php

declare(strict_types=1);

namespace App\Domain\Reservation;

use App\Domain\Model\Model;
use App\Domain\Room\Room;
use App\Domain\User\User;
use DateTime;


class Reservation extends Model
{
    public string      $title;
    public string      $description;
    public Room        $room;
    public User        $user;
    public DateTime    $plannedStart;
    public DateTime    $plannedEnd;
    public ?DateTime    $actualStart;
    public ?DateTime    $actualEnd;
    public bool        $confirmed;
    public ?User        $confirmedBy;
    public ?DateTime    $confirmedAt;

    public int $userId;
    public int $confirmingUserId;
    public int $roomId;


    public function __construct(
        int         $id,
        string      $title,
        string      $description,
        Room         $room,
        User         $user,
        DateTime    $planned_start,
        DateTime    $planned_end,
        ?DateTime    $actual_start,
        ?DateTime    $actual_end,
        bool        $confirmed,
        ?User         $confirmed_by,
        ?DateTime    $confirmed_at,
        DateTime    $created,
        DateTime    $updated
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
        $this->confirmed = $confirmed;
        $this->confirmedBy = $confirmed_by;
        $this->confirmedAt = $confirmed_at;
    }


    /**
     * {@inheritdoc}
     * @throws DomainConflictException
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
        return [
            'id' => $this->id,
            'title' => $this->title,
            'description' => $this->description,
            'room' => $this->room ?? $this->roomId,
            'user' => $this->user ?? $this->userId,
            'plannedStart' => $this->plannedStart->format('c'),
            'plannedEnd' => $this->plannedEnd->format('c'),
            'actualStart' => $this->actualStart && $this->actualStart->format('c'),
            'actualEnd' => $this->actualEnd && $this->actualEnd->format('c'),
            'confirmed' => $this->confirmed,
            'confirmedBy' => $this->confirmedBy,
            'confirmedAt' => $this->confirmedAt && $this->confirmedAt->format('c'),
            'created' => $this->created->format('c'),
            'updated' => $this->updated->format('c'),
        ];
    }
}
