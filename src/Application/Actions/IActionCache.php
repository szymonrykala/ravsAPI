<?php
declare(strict_types=1);

namespace App\Application\Actions;

use App\Domain\Model\Model;

Interface IActionCache{

    /**
     * @param string $key
     * @return bool
     */
    public function contain(string $key):bool;

    /**
     * @param string $key
     * @return Model
     */
    public function get(string $key): Model;
    
    /**
     * @param string $key
     * @param Model $model
     */    
    public function set(string $key, Model $model): void;
    
    /**
     * @return void
     */
    public function flush():void;

}