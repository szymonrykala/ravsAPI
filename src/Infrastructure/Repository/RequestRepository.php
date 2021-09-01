<?php
declare(strict_types=1);

namespace App\Infrastructure\Repository;

use Psr\Container\ContainerInterface;

use App\Application\Settings\SettingsInterface;
use App\Domain\Request\RequestRepositoryInterface;
use App\Domain\Request\Request;
use stdClass;
use DateTime;


class RequestRepository extends BaseRepository implements RequestRepositoryInterface{

    protected string $table = 'request';

    /**
     * @param array $data from database
     * @return Request
     */
    protected function newItem(array $data): Request
    {
        return new Request(
            (int)   $data['id'],
                    $data['method'],
                    $data['endpoint'],
            (int)   $data['user_id'],
                    $data['payload'],
                    new DateTime($data['created']),
                    new DateTime($data['updated'])
        );
    }

    /**
     * {@inheritdoc}
     */
    public function create(string $method, string $uriPath, int $userId, stdClass $payload): void
    {
        $sql = "INSERT INTO `$this->table`(`method`,`endpoint`,`user_id`,`payload`)
            VALUES(:method,:endpoint,:userId,:payload)";

        $params = [
            ':method' => $method,
            ':endpoint' => $uriPath,
            ':userId' => $userId,
            ':payload' => json_encode($payload)
        ];
        $this->db->query($sql, $params);
    }

    /**
     * {@inheritdoc}
     */
    public function deleteList(array $ids): void
    {
        $sql = "DELETE FROM `$this->table` WHERE `id` IN (:ids)";
        
        $str_list = '';
        foreach($ids as $id) $str_list .= $id.',';
        
        $params = [':ids' => $str_list];

        $this->db->query($sql, $params);
    }
}