<?php

declare(strict_types=1);

namespace App\Infrastructure\Repository;


use App\Domain\Request\IRequestRepository;
use App\Domain\Request\Request;
use App\Utils\JsonDateTime;
use Psr\Http\Message\ServerRequestInterface;



final class RequestRepository extends BaseRepository implements IRequestRepository
{

    protected string $table = '`request`';

    /**
     * {@inheritDoc}
     */
    public function whereLIKE(array $searchParams): IRequestRepository
    {
        foreach ($searchParams as $key => $value) {
            $this->SQLwhere .= " AND $this->table.`$key` LIKE :$key";
            $this->params[":$key"] = $value.'%' ;
        }

        return $this;
    }

    /**
     * {@inheritDoc}
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
    public function create(ServerRequestInterface $request): void
    {
        $sql = "INSERT INTO $this->table(`method`, `endpoint`, `user_id`, `payload`, `time`)
            VALUES(:method, :endpoint, :userId, :payload, :time)";

        $params = [
            ':method' => $request->getMethod(),
            ':endpoint' => $request->getUri()->getPath(),
            ':userId' => $request->getAttribute('session')->userId ?? NULL,
            ':payload' => json_encode($request->getParsedBody()),
            ':time' => microtime(true) - $request->getServerParams()['REQUEST_TIME_FLOAT'],
        ];
        $this->db->query($sql, $params);
    }
}
