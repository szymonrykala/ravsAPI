<?php
declare(strict_types=1);

namespace App\Application\Actions;

use App\Utils\Pagination;
use JsonSerializable;

class ActionPayload implements JsonSerializable
{
    /**
     * @var int
     */
    private $statusCode;

    /**
     * @var array|object|null
     */
    private $data;

    /**
     * @var ActionError|null
     */
    private $error;

    private ?Pagination $pagination;


    /**
     * @param int                   $statusCode
     * @param array|object|null     $data
     * @param ActionError|null      $error
     */
    public function __construct(
        int $statusCode = 200,
        $data = NULL,
        ?ActionError $error = NULL,
        ?Pagination $pagination = NULL
    ) {
        $this->statusCode = $statusCode;
        $this->data = $data;
        $this->error = $error;
        $this->pagination = $pagination;
    }

    /**
     * @return int
     */
    public function getStatusCode(): int
    {
        return $this->statusCode;
    }

    /**
     * @return array|null|object
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * @return ActionError|null
     */
    public function getError(): ?ActionError
    {
        return $this->error;
    }

    /**
     * @return array
     */
    public function jsonSerialize()
    {
        $payload = [
            'statusCode' => $this->statusCode,
        ];

        $this->pagination && $payload['pagination'] = $this->pagination;

        if ($this->data !== null) {
            $payload['data'] =  $this->data;
        } elseif ($this->error !== null) {
            $payload['error'] = $this->error;
        }

        return $payload;
    }
}
