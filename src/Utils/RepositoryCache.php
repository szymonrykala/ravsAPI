<?php
declare(strict_types=1);

namespace App\Utils;

use App\Domain\Model\Model;

class RepositoryCache
{
    private array $memory;


    public function __construct()
    {
        $this->memory = [];
    }

    /**
     * adds element to cache
     */
    public function add(string $key ,Model $item)
    {
        $this->memory[$key] = $item;
    }

    /**
     * gets element from cache
     */
    public function get(string $key): ?Model
    {
        return isset($this->memory[$key])? $this->memory[$key]: NULL;
    }

}