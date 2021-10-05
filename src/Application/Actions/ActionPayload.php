<?php

declare(strict_types=1);

namespace App\Application\Actions;

use App\Utils\Pagination;
use JsonSerializable;

class ActionPayload implements JsonSerializable
{
    private int $statusCode;

    /**
     * @var array|object|null
     */
    private $data;
    private ?ActionError $error;
    private ?Pagination $pagination;


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
     * get satus code of the payload
     */
    public function getStatusCode(): int
    {
        return $this->statusCode;
    }

    /**
     * get data of the payload
     * @return array|object|null
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * get error of the action
     */
    public function getError(): ?ActionError
    {
        return $this->error;
    }

    /**
     * {@inheritDoc}
     */
    public function jsonSerialize(): array
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
