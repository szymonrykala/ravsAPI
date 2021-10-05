<?php

declare(strict_types=1);

namespace App\Utils;



class QueryBuilder
{
    private string $SQL;
    private array $queryParams;

    private string $table;

    private string $head = '';
    private string $where = '';
    private string $order = '';
    private string $limit = '';


    public function __construct()
    {
        $this->SQL = '';
        $this->queryParams = [];
    }

    public function setTable(string $table): void
    {
        $this->table = $table;
    }

    public function select(array $fields = []): QueryBuilder
    {
        $this->head = 'SELECT';

        if (empty($fields)) {
            $this->head .= ' * ';
        } else {
            $this->head .= implode(', ', $fields);
        }

        $this->head .= 'FROM ' . $this->table;

        return $this;
    }

    public function where(string $name, $value): QueryBuilder
    {
        $this->where .= " WHERE `${name}` = :$name";
        $this->queryParams[':' . $name] = $value;

        return $this;
    }

    public function and(string $name, $value): QueryBuilder
    {
        $this->where .= " AND `${name}` = :$name";
        $this->queryParams[':' . $name] = $value;

        return $this;
    }

    public function or(string $name, $value): QueryBuilder
    {
        $this->where .= " OR `${name}` = :$name";
        $this->queryParams[':' . $name] = $value;

        return $this;
    }

    public function orderBy(string $name, string $direction = 'DESC'): QueryBuilder
    {
        $this->order = " ORDER BY `:${$name}` ${direction}";
        $this->queryParams[':' . $name] = $name;

        return $this;
    }

    public function limit(int $first, int $second): QueryBuilder
    {
        $this->limit = " LIMIT ${first},${$second}";

        return $this;
    }

    public function count():QueryBuilder
    {
        str_replace('*', 'COUNT(`id`) as items_count', $this->head);
        return $this;
    }


    public function SQL():string
    {
        return $this->head
                .$this->where
                .$this->order
                .$this->limit;
    }

    public function params():array
    {
        return $this->queryParams;
    }
}
