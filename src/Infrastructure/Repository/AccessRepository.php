<?php

declare(strict_types=1);

namespace App\Infrastructure\Repository;


use App\Domain\Access\{
    IAccessRepository,
    Access,
    AccessDeleteException
};
use App\Domain\Configuration\IConfigurationRepository;
use App\Domain\Model\Model;
use App\Utils\JsonDateTime;
use Psr\Container\ContainerInterface;


final class AccessRepository extends BaseRepository implements IAccessRepository
{
    /** {@inheritDoc} */
    protected string $table = '`access`';


    public function __construct(
        ContainerInterface $di,
        private IConfigurationRepository $config
    ) {
        parent::__construct($di);
    }

    /**
     * {@inheritDoc}
     */
    protected function newItem(array $data): Access
    {
        return new Access(
            (int)   $data['id'],
            $data['name'],
            (bool)  $data['owner'],
            (bool)  $data['access_admin'],
            (bool)  $data['premises_admin'],
            (bool)  $data['keys_admin'],
            (bool)  $data['reservations_admin'],
            (bool)  $data['reservations_ability'],
            (bool)  $data['logs_admin'],
            (bool)  $data['stats_viewer'],
            new JsonDateTime($data['created']),
            new JsonDateTime($data['updated']),
        );
    }

    /**
     * {@inheritDoc}
     * @throws AccessDeleteException
     */
    public function delete(Model $access): void
    {
        $defaultAccessId = $this->config->load()->defaultUserAccess;

        if ($access->id === 1 || $access->id === $defaultAccessId) {
            throw new AccessDeleteException();
        }
        parent::delete($access);
    }

    /**
     * {@inheritDoc}
     */
    public function save(Access $access): void
    {
        $access->validate();

        $sql = "UPDATE $this->table SET
                    `name` = :name,
                    `owner` = :owner,
                    `access_admin` = :accessAdmin,
                    `premises_admin` = :premisesAdmin,
                    `keys_admin` = :keysAdmin,
                    `reservations_admin` = :reservationsAdmin,
                    `reservations_ability` = :reservationsAbility,
                    `logs_admin` = :logsAdmin,
                    `stats_viewer` = :statsViewer
                WHERE `id` = :id";

        $params = [
            ':id' => $access->id,
            ':name' => ucfirst($access->name),
            ':owner' => (int) $access->owner,
            ':accessAdmin' => (int) $access->accessAdmin,
            ':premisesAdmin' => (int) $access->premisesAdmin,
            ':keysAdmin' => (int) $access->keysAdmin,
            ':reservationsAdmin' => (int) $access->reservationsAdmin,
            ':reservationsAbility' => (int) $access->reservationsAbility,
            ':logsAdmin' => (int) $access->logsAdmin,
            ':statsViewer' => (int) $access->statsViewer,
        ];

        $this->db->query($sql, $params);
    }

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
    ): int {
        $sql = "INSERT INTO $this->table(
                    `name`,
                    `owner`,
                    `access_admin`,
                    `premises_admin`,
                    `keys_admin`,
                    `reservations_admin`,
                    `reservations_ability`,
                    `logs_admin`,
                    `stats_viewer`
                )
                VALUES (
                    :name,
                    :owner,
                    :accessAdmin,
                    :premisesAdmin,
                    :keysAdmin,
                    :reservationsAdmin,
                    :reservationsAbility,
                    :logsAdmin,
                    :statsViewer
                )";

        $params = [
            ':name' => ucfirst($name),
            ':owner' => (int) $owner,
            ':accessAdmin' => (int) $accessAdmin,
            ':premisesAdmin' => (int) $premisesAdmin,
            ':keysAdmin' => (int) $keysAdmin,
            ':reservationsAdmin' => (int) $reservationsAdmin,
            ':reservationsAbility' => (int) $reservationsAbility,
            ':logsAdmin' => (int) $logsAdmin,
            ':statsViewer' => (int) $statsViewer,
        ];

        $this->db->query($sql, $params);
        return $this->db->lastInsertId();
    }
}
