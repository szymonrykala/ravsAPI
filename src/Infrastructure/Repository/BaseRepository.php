<?php

declare(strict_types=1);

namespace App\Infrastructure\Repository;

use App\Infrastructure\Database\IDatabase;
use App\Domain\Model\IRepository;
use App\Domain\Model\Model;


use App\Domain\Exception\DomainResourceNotFoundException;
use App\Utils\Pagination;
use App\Utils\RepositoryCache;
use Psr\Container\ContainerInterface;


abstract class BaseRepository implements IRepository
{
    protected IDatabase $db;
    private Pagination $pagination;
    protected RepositoryCache $cache;
    protected string $configTable = 'configuration';

    protected string $SQL;
    protected string $SQLwhere = ' WHERE 1=1';
    protected string $SQLlimit = '';
    protected string $SQLorder = ' ORDER BY `created` DESC';

    protected array $params = [];

    /** Database table name */
    protected string $table;
    protected string $sql = ' ';


    public function __construct(
        ContainerInterface $di
    ) {
        $this->db = $di->get(IDatabase::class);
        $this->db->connect();

        $this->SQL = 'SELECT * FROM ' . $this->table;
        $this->cache = new RepositoryCache();
    }

    /**
     * Creates new Domain object from provided data
     * @return Model
     */
    abstract protected function newItem(array $data): Model;


    /**
     * {@inheritDoc}
     */
    public function where(array $searchParams): IRepository
    {
        foreach ($searchParams as $key => $value) {
            $this->SQLwhere .= " AND `$this->table`.`$key`=:$key";
            $this->params[":$key"] = $value;
        }

        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function orderBy(string $name, string $direction = 'DESC'): IRepository
    {
        $this->SQLorder = " ORDER BY `${name}` ${direction}";

        return $this;
    }


    /**
     * {@inheritDoc}
     */
    public function setPagination(Pagination &$pagination): IRepository
    {
        $this->pagination = $pagination;
        /** @var string localSQL */
        $localSQL = $this->SQL . $this->SQLwhere;

        $localSQL = preg_replace('/SELECT(.*)FROM\s(\w+)(.*)/i', 'SELECT COUNT($2.id) as items_count FROM $2 $3', $localSQL);;
        // print_r($localSQL);

        $result = $this->db->query($localSQL, $this->params)[0];

        $this->pagination->calculatePagesCount((int) $result['items_count']);
        $this->SQLlimit = $this->pagination->generateSQL();

        return $this;
    }


    /**
     * {@inheritDoc}
     */
    public function all(): array
    {
        $results = $this->executeQuery();

        return array_map(fn ($data) => $this->newItem($data), $results);
    }


    /**
     * {@inheritDoc}
     */
    public function one(): Model
    {
        $results = $this->executeQuery();
        $item = array_pop($results);

        if (empty($item)) {
            throw new DomainResourceNotFoundException();
        }

        return $this->newItem($item);
    }

    protected function executeQuery(): array
    {
        // echo "\n" . $this->SQL . $this->SQLwhere . $this->SQLorder . $this->SQLlimit . "\n";

        $result =  $this->db->query(
            $this->SQL . $this->SQLwhere . $this->SQLorder . $this->SQLlimit,
            $this->params
        );
        $this->SQLwhere = ' WHERE 1=1';
        $this->params = [];
        return $result;
    }

    /** 
     * {@inheritDoc}
     */
    public function byId(int $id): Model
    {
        $result = $this->db->query(
            "SELECT * FROM $this->table WHERE id=:id",
            [':id' => $id]
        );

        if (empty($result)) throw new DomainResourceNotFoundException();

        $key = get_class($this) . $id;

        $item = $this->cache->get($key);
        if (!empty($item)) return $item;

        $item = $this->newItem(array_pop($result));
        $this->cache->add($key, $item);

        return $item;
    }

    /**
     * {@inheritDoc}
     */
    public function delete(Model $object): void
    {
        $this->db->query(
            "DELETE FROM `$this->table` WHERE `id` = :id",
            [':id' => $object->id]
        );
    }
}
