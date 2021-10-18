<?php

declare(strict_types=1);

namespace App\Domain\Access;

use App\Domain\Model\IRepository;



interface IAccessRepository extends IRepository
{

    /**
     * {@inheritDoc}
     * @throws AccessUpdateException
     */
    public function save(Access $access): void;

    /**
     * {@inheritDoc}
     */
    public function create(
        string $name,
        bool $owner,
        bool $accessAdmin,
        bool $premisesAdmin,
        bool $keysAdmin,
        bool $reservationsAdmin,
        bool $reservationsAbility,
        bool $logsAdmin,
        bool $statsViewer
    ): int;
}
