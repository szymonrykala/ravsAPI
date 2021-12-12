<?php

declare(strict_types=1);

namespace App\Domain\Request;

use App\Domain\Model\Model;
use App\Utils\JsonDateTime;


final class Request extends Model
{
    public function __construct(
        public int $id,
        public string $method,
        public string $endpoint,
        public int $userId,
        public string $payload,
        public float $time,
        public JsonDateTime $created,
        public JsonDateTime $updated
    ) {
        parent::__construct($id, $created, $updated);
    }

    /**
     * {@inheritDoc}
     */
    public function jsonSerialize(): array
    {
        return array_merge(
            [
                'method' => $this->method,
                'endpoint' => $this->endpoint,
                'user' => $this->user ?? $this->userId,
                'payload' => $this->payload,
                'time' => $this->time,
            ],
            parent::jsonSerialize()
        );
    }
}
