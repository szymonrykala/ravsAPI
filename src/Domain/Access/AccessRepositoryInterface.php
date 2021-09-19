<?php

declare(strict_types=1);

namespace App\Domain\Access;

use App\Domain\Model\Model;
use App\Domain\Model\RepositoryInterface;



interface AccessRepositoryInterface extends RepositoryInterface
{

    /**
     * @param Access $access
     * @throws AccessUpdateException
     */
    public function save(Access $access): void;

    /**
     * @param string    name
     * @param bool      owner
     * @param bool      accessAdmin
     * @param bool      premisesAdmin
     * @param bool      keysAdmin
     * @param bool      reservationsAdmin
     * @param bool      reservationsAbility
     * @param bool      logsAdmin
     * @param bool      statsViewer
     * @return int
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
