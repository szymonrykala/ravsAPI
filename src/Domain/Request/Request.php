<?php

declare(strict_types=1);

namespace App\Domain\Request;

use App\Domain\Model\Model;
use App\Domain\User\User;
use App\Utils\JsonDateTime;


final class Request extends Model
{

    public string $method;
    public string $endpoint;
    public User $user;
    public array $payload;
    public float $time;

    public int $userId;

    /**
     * @param int       id
     * @param string    path
     * @param string    method
     * @param string    endpoint
     * @param int       userId
     * @param string    payload
     * @param float     time
     * @param string    created
     * @param string    updated
     */
    public function __construct(
        int $id,
        string $method,
        string $endpoint,
        int $userId,
        string $payload,
        float $time,
        JsonDateTime $created,
        JsonDateTime $updated
    ) {
        parent::__construct($id, $created, $updated);

        $this->method = $method;
        $this->endpoint = $endpoint;
        $this->userId = $userId;
        $this->payload = json_decode($payload, TRUE);
        $this->time = $time;
    }

    /**
     * @return array
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
