<?php

declare(strict_types=1);

namespace App\Infrastructure\Repository;


use App\Domain\Access\{
    AccessRepositoryInterface,
    Access,
    AccessDeleteException
};

use DateTime;


class AccessRepository extends BaseRepository implements AccessRepositoryInterface
{
    protected string $table = 'access';

    /**
     * @param array $data from database
     * @return Access
     */
    protected function newItem(array $data): Access
    {
        return new Access(
            (int)   $data['id'],
                    $data['name'],
            (bool)  $data['access_admin'],
            (bool)  $data['premises_admin'],
            (bool)  $data['keys_admin'],
            (bool)  $data['reservations_admin'],
            (bool)  $data['reservations_ability'],
            (bool)  $data['logs_admin'],
            (bool)  $data['stats_viewer'],
            (bool)  $data['reports_viewer'],
                    new DateTime($data['created']),
                    new DateTime($data['updated']),
        );
    }


    /**
     * {@inheritdoc}
     */
    public function deleteById(int $id): void
    {
        if ($id === 1) {
            throw new AccessDeleteException();
        }
        $sql = "DELETE FROM `$this->table` WHERE `id` = :id";
        $params = [':id' => $id];
        $this->db->query($sql, $params);
    }

    /**
     * {@inheritdoc}
     */
    public function save(Access $access): void
    {
        $access->validate();

        $sql = "UPDATE `$this->table` SET
                    `name` = :name,
                    `access_admin` = :accessAdmin,
                    `premises_admin` = :premisesAdmin,
                    `keys_admin` = :keysAdmin,
                    `reservations_admin` = :reservationsAdmin,
                    `reservations_ability` = :reservationsAbility,
                    `logs_admin` = :logsAdmin,
                    `stats_viewer` = :statsViewer,
                    `reports_viewer` = :reportsViewer
                WHERE `id` = :id";

        $params = [
            ':id' => $access->id,
            ':name' => ucfirst($access->name),
            ':accessAdmin' => (int) $access->accessAdmin,
            ':premisesAdmin' => (int) $access->premisesAdmin,
            ':keysAdmin' => (int) $access->keysAdmin,
            ':reservationsAdmin' => (int) $access->reservationsAdmin,
            ':reservationsAbility' => (int) $access->reservationsAbility,
            ':logsAdmin' => (int) $access->logsAdmin,
            ':statsViewer' => (int) $access->statsViewer,
            ':reportsViewer' => (int) $access->reportsViewer,
        ];

        $this->db->query($sql, $params);
    }

    /**
     * {@inheritdoc}
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
    ): int {
        $sql = "INSERT `$this->table`(
                    `name`,
                    `access_admin`,
                    `premises_admin`,
                    `keys_admin`,
                    `reservations_admin`,
                    `reservations_ability`,
                    `logs_admin`,
                    `stats_viewer`,
                    `reports_viewer`
                )
                VALUES (
                    :name,
                    :accessAdmin,
                    :premisesAdmin,
                    :keysAdmin,
                    :reservationsAdmin,
                    :reservationsAbility,
                    :logsAdmin,
                    :statsViewer,
                    :reportsViewer
                )";

        $params = [
            ':name' => ucfirst($name),
            ':accessAdmin' => (int) $accessAdmin,
            ':premisesAdmin' => (int) $premisesAdmin,
            ':keysAdmin' => (int) $keysAdmin,
            ':reservationsAdmin' => (int) $reservationsAdmin,
            ':reservationsAbility' => (int) $reservationsAbility,
            ':logsAdmin' => (int) $logsAdmin,
            ':statsViewer' => (int) $statsViewer,
            ':reportsViewer' => (int) $reportsViewer,
        ];

        $this->db->query($sql, $params);
        return $this->db->lastInsertId();
    }
}
