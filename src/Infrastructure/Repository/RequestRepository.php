<?php

declare(strict_types=1);

namespace App\Infrastructure\Repository;


use App\Domain\Request\RequestRepositoryInterface;
use App\Domain\Request\Request;
use App\Utils\JsonDateTime;
use Psr\Http\Message\ServerRequestInterface;
use stdClass;



final class RequestRepository extends BaseRepository implements RequestRepositoryInterface
{

    protected string $table = 'request';

    /**
     * {@inheritDoc}
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
            (float) $data['time'],
            new JsonDateTime($data['created']),
            new JsonDateTime($data['updated'])
        );
    }

    /**
     * {@inheritDoc}
     */
    public function create(ServerRequestInterface $request): void {
        $sql = "INSERT INTO `$this->table`(`method`,`endpoint`,`user_id`,`payload`, `time`)
            VALUES(:method,:endpoint,:userId,:payload, :time)";

        $params = [
            ':method' => $request->getMethod(),
            ':endpoint' => $request->getUri()->getPath(),
            ':userId' => $request->getAttribute('session')->userId ?? NULL,
            ':payload' => json_encode($request->getParsedBody()),
            ':time' => microtime(true) - $request->getServerParams()['REQUEST_TIME_FLOAT'],
        ];
        $this->db->query($sql, $params);
    }

    /**
     * {@inheritDoc}
     */
    public function deleteList(array $ids): void
    {
        $sql = "DELETE FROM `$this->table` WHERE `id` IN (:ids)";

        $str_list = implode(',', $ids);

        $params = [':ids' => $str_list];

        $this->db->query($sql, $params);
    }
}
