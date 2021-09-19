<?php

declare(strict_types=1);

namespace App\Utils;

use DateTime;
use JsonSerializable;

Class JsonDateTime extends DateTime implements JsonSerializable
{
    /**
     * {@inheritdoc}
     */
    public function jsonSerialize():string
    {
        return $this->format('c'); //DateTime::ISO8601
    }

    /**
     * formats datetime to format acceptable by database
     * @return string
     */
    public function __toString():string
    {
        return $this->format('Y-m-d H-i-s');
    }

    public function getTime():string
    {
        return $this->format('H:i:s');
    }

    public function getDate():string
    {
        return $this->format('Y-m-d');
    }
}