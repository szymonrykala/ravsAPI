<?php
declare(strict_types=1);

namespace App\Domain\Exception;

use Exception;

abstract class DomainException extends Exception
{
    public $message;

    // public function __construct(string $customMessage = "")
    // {
    //     if( !empty($customMessage) ){
    //         $this->message = $customMessage;
    //     }
    // }
}
