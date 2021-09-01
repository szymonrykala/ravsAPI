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
    public DateTime    $planned_start;
    public DateTime    $planned_end;
    public DateTime    $actual_start;
    public DateTime    $actual_end; 
    public bool        $confirmed;
    public User        $confirmed_by;
    public DateTime    $confirmed_at;

    public int $userId;
    public int $confirmingUserId;
    public int $roomId;


    public function __construct(
        int         $id,
        string      $title,
        string      $description,
        int         $room,
        int         $user,
        DateTime    $planned_start,
        DateTime    $planned_end,
        DateTime    $actual_start,
        DateTime    $actual_end,
        bool        $confirmed,
        int         $confirmed_by,
        DateTime    $confirmed_at,
        DateTime    $created,
        DateTime    $updated
    )
    {
        parent::__construct($id, $created, $updated);

        $this->title = $title;
        $this->description = $description;
        $this->room = $room;
        $this->user = $user;
        $this->planned_start = $planned_start;
        $this->planned_end = $planned_end;
        $this->actual_start = $actual_start;
        $this->actual_end = $actual_end;
        $this->confirmed = $confirmed;
        $this->confirmed_by = $confirmed_by;
        $this->confirmed_at = $confirmed_at;
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
    public function jsonSerialize():array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'description' => $this->description,
            'room' => $this->room ?? $this->roomId,
            'user' => $this->user ?? $this->userId,
            'planned_start' => $this->planned_start->format('c'),
            'planned_end' => $this->planned_end->format('c'),
            'actual_start' => $this->actual_start->format('c'),
            'actual_end' => $this->actual_end->format('c'),
            'confirmed' => $this->confirmed,
            'confirmed_by' => $this->confirmed_by,
            'confirmed_at' => $this->confirmed_at->format('c'),
            'created' => $this->created,
            'updated' => $this->updated,
        ];
    }
}