<?php

declare(strict_types=1);

namespace App\Utils;

use App\Domain\Exception\DomainBadRequestException;
use DateTime;
use JsonSerializable;


final Class JsonDateTime extends DateTime implements JsonSerializable
{

    public function __construct($dateString)
    {
        try{
            parent::__construct($dateString);
        }catch(\Exception $ex){
            throw new DomainBadRequestException("Nieprawidłowa wartość daty '$dateString'.");
        }
    }

    /**
     * {@inheritDoc}
     */
    public function jsonSerialize():string
    {
        return $this->format('c'); //DateTime::ISO8601
    }

    /**
     * formats datetime to format acceptable by database
     */
    public function __toString():string
    {
        return $this->format('Y-m-d H:i:s');
    }

    /** returns time in format H:i:s */
    public function getTime():string
    {
        return $this->format('H:i:s');
    }

    /** returns date in format Y-m-d */
    public function getDate():string
    {
        return $this->format('Y-m-d');
    }
}