<?php

declare(strict_types=1);

namespace App\Domain\Access;

use App\Domain\Model\Model;
use App\Utils\JsonDateTime;


final class Access extends Model
{
    public function __construct(
        public int $id,
        public string $name,
        public bool $owner,
        public bool $accessAdmin,
        public bool $premisesAdmin,
        public bool $keysAdmin,
        public bool $reservationsAdmin,
        public bool $reservationsAbility,
        public bool $logsAdmin,
        public bool $statsViewer,
        public JsonDateTime $created,
        public JsonDateTime $updated
    ) {
        parent::__construct($id, $created, $updated);
    }

    /**
     * {@inheritDoc}
     */
    public function validateCallback(): void
    {
        if ($this->id === 1) {
            throw new AccessUpdateException();
        }
    }

    /**
     * {@inheritDoc}
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
