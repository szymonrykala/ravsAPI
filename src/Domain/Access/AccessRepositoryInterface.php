<?php

declare(strict_types=1);

namespace App\Domain\Access;

use App\Domain\Model\RepositoryInterface;



interface AccessRepositoryInterface extends RepositoryInterface
{

    /**
     * @param int $id
     * @throws AccessDeleteException
     */
    public function deleteById(int $id): void;

    /**
     * @param Access $access
     * @throws AccessUpdateException
     */
    public function save(Access $access): void;

    /**
     * @param string    name;
     * @param bool      accessAdmin
     * @param bool      premisesAdmin
     * @param bool      keysAdmin
     * @param bool      reservationsAdmin
     * @param bool      reservationsAbility
     * @param bool      logsAdmin
     * @param bool      statsViewer
     * @param bool      reportsViewer;
     * @return int
     */
    public function create(
        string $name,
        bool $accessAdmin,
        bool $premisesAdmin,
        bool $keysAdmin,
        bool $reservationsAdmin,
        bool $reservationsAbility,
        bool $logsAdmin,
        bool $statsViewer,
        bool $reportsViewer
    ): int;
}
