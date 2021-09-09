<?php

declare(strict_types=1);

namespace App\Infrastructure\Repository;

use Psr\Container\ContainerInterface;
use App\Infrastructure\Database\IDatabase;
use App\Domain\Model\RepositoryInterface;
use App\Domain\Model\Model;


use App\Domain\Exception\DomainResourceNotFoundException;
use App\Utils\RepositoryCache;

abstract class BaseRepository implements RepositoryInterface
{

    protected RepositoryCache $cache;

    private string $dateSearch = "";
    private string $limit = "";

    protected string $table;
    protected string $sql = ' ';
    protected array $params = [];

    /**
     * @param IDatabase db database
     */
    public function __construct(IDatabase $db)
    {
        $this->db = $db;
        $this->db->connect();
        $this->cache = new RepositoryCache();
    }

    /**
     * @param array $data
     * @return Model
     */
    abstract protected function newItem(array $data): Model;

    /**
     * @return array
     */
    private function executeQuery(): array
    {
        $data = $this->db->query($this->sql, $this->params);

        $this->sql = ' ';
        $this->params = [];
        return $data;
    }

    /**
     * {@inheritdoc}
     */
    public function where(array $searchParams): BaseRepository
    {
        $this->sql = ' WHERE 1=1';
        foreach ($searchParams as $key => $value) {
            $this->sql .= " AND `$this->table`.`$key`=:$key";
            $this->params[":$key"] = $value;
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function withDates(array $dateFields): self
    {
        /**
         * [{
         *      'name' = any timestamped field
         *      'operator' = [<>=]
         *      'value' = timestamp ISO format
         *  }, {...}]
         */
        foreach ($dateFields as $key => $field) {
            $this->dateSearch .= " AND `$field->name` $field->operator TIMESTAMP(:$key$field->name)";
            $this->params[$key . $field->name] = $field->value;
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function page(int $number, int $limit = 20): array
    {

        $localSql = 'SELECT count(id) as `rows_count` FROM '
            . $this->table
            . $this->sql
            . $this->dateSearch;

        $rowsCount = (int) $this->db->query($localSql, $this->params)[0]['rows_count'];

        $this->limit = 'LIMIT ' . $limit * ($number - 1) . ',' . $limit;

        return [
            'page' => $number,
            'perPage' => $limit,
            'pagesCount' => ceil($rowsCount / $limit),
            'data' => $this->all()
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function all(): array
    {
        $this->sql = 'SELECT * FROM '
            . $this->table
            . $this->sql
            . $this->dateSearch
            . $this->limit;

        $data = $this->executeQuery();

        $items = [];
        foreach ($data as $userData) {
            array_push($items, $this->newItem($userData));
        }

        return $items;
    }

    /**
     * {@inheritdoc}
     */
    public function byId(int $id): Model
    {
        $sql = "SELECT * FROM `$this->table` WHERE `id` = :id";

        $item = $this->cache->get($sql.$id);
        if (!empty($item)) return $item;

        $params = [':id' => $id];

        $result = $this->db->query($sql, $params);
        $item = array_pop($result);

        if (empty($item)) {
            throw new DomainResourceNotFoundException();
        }

        $item = $this->cache->add($sql.$id, $this->newItem($item));

        return $item;
    }

    /**
     * {@inheritdoc}
     */
    public function delete(Model $object): void
    {
        $this->db->query(
            "DELETE FROM `$this->table` WHERE `id` = :id",
            [':id' => $object->id]
        );
    }
}
