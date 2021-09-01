<?php
declare(strict_types=1);

namespace App\Application\Actions;

use App\Domain\Model\Model;


class ActionMemoryCache implements IActionCache{

    private array $memory = [];

    public function __construst(array $initValue=[])
    {
        $this->memory = $initValue;
    }

    /**
     * {@inheritdoc}
     */

    public function contain(string $key):bool
    {
        return array_key_exists($key, $this->memory);
    }

    /**
     * {@inheritdoc}
     */
    public function get(string $key): Model
    {
        return $this->memory[$key];
    }

    /**
     * {@inheritdoc}
     */
    public function set(string $key, Model $model): void
    {
        $this->memory[$key] = $model;
    }

    /**
     * {@inheritdoc}
     */
    public function flush():void
    {
        $this->memory = [];
    }
}