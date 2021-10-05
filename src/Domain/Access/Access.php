<?php

declare(strict_types=1);

namespace App\Domain\Access;

use App\Domain\Model\Model;
use App\Utils\JsonDateTime;


class Access extends Model
{

    public string $name;
    public bool $owner;
    public bool $accessAdmin;
    public bool $premisesAdmin;
    public bool $keysAdmin;
    public bool $reservationsAdmin;
    public bool $reservationsAbility;
    public bool $logsAdmin;
    public bool $statsViewer;


    /**
     * @param int       id
     * @param string    name;
     * @param bool      owner;
     * @param bool      accessAdmin
     * @param bool      premisesAdmin
     * @param bool      keysAdmin
     * @param bool      reservationsAdmin
     * @param bool      reservationsAbility
     * @param bool      logsAdmin
     * @param bool      statsViewer
     * @param string    created,
     * @param string    updated
     */
    public function __construct(
        int $id,
        string $name,
        bool $owner,
        bool $accessAdmin,
        bool $premisesAdmin,
        bool $keysAdmin,
        bool $reservationsAdmin,
        bool $reservationsAbility,
        bool $logsAdmin,
        bool $statsViewer,
        JsonDateTime $created,
        JsonDateTime $updated
    ) {
        parent::__construct($id, $created, $updated);


        $this->name = $name;
        $this->owner = $owner;
        $this->accessAdmin = $accessAdmin;
        $this->premisesAdmin = $premisesAdmin;
        $this->keysAdmin = $keysAdmin;
        $this->reservationsAdmin = $reservationsAdmin;
        $this->reservationsAbility = $reservationsAbility;
        $this->logsAdmin = $logsAdmin;
        $this->statsViewer = $statsViewer;
    }

    /**
     * {@inheritdoc}
     */
    public function validateCallback(): void
    {
        if ($this->id === 1) {
            throw new AccessUpdateException();
        }
    }

    /**
     * @return array
     */
    public function jsonSerialize(): array
    {
        return array_merge(
            [
                "name" => $this->name,
                "owner" => $this->owner,
                "accessAdmin" => $this->accessAdmin,
                "premisesAdmin" => $this->premisesAdmin,
                "keysAdmin" => $this->keysAdmin,
                "reservationsAdmin" => $this->reservationsAdmin,
                "reservationsAbility" => $this->reservationsAbility,
                "logsAdmin" => $this->logsAdmin,
                "statsViewer" => $this->statsViewer,
            ],
            parent::jsonSerialize()
        );
    }
}
