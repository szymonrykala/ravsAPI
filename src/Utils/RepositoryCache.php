<?php
declare(strict_types=1);

namespace App\Utils;

use App\Domain\Model\Model;

class RepositoryCache
{
    /**
     * @var array memory
     */
    private array $memory;


    public function __construct()
    {
        $this->memory = [];
    }

    /**
     * @param string key,
     * @param Model item
     * @return Model item
     */
    public function add(string $key ,Model $item)
    {
        $this->memory[$key] = $item;
    }

    /**
     * @param string key
     * @return Model|NULL
     */
    public function get(string $key): ?Model
    {
        return isset($this->memory[$key])? $this->memory[$key]: NULL;
    }

}