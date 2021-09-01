<?php
declare(strict_types=1);

namespace App\Domain\Model;

use stdClass;
use JsonSerializable;
use DateTime;
use App\Domain\ModelPropertyNotExistException;


abstract class Model implements JsonSerializable{

    public int $id;
    public DateTime $created;
    public DateTime $updated;

    abstract public function jsonSerialize():array;

    public function __construct(int $id, DateTime $created, DateTime $updated)
    {
        $this->id = $id;

        $this->created = $created;
        $this->updated = $updated;
    }

    public function __set(string $name, $value){
        throw new ModelPropertyNotExistException($name);
    }

    /**
     * Updates model properties
     * @param stdClass $form
     * @throws TypeError
     */
    public function update(stdClass $form): void
    {
        foreach($form as $key => $value) $this->$key = $value;
    }

    /**
     * optional model object validation rules bfore saving
     * @return void
     */
    protected function validateCallback():void
    {}

    /**
     * validation trigger fo validateCallback
     * @return void
     */
    public function validate():void
    {
        $this->validateCallback();
    }

}
