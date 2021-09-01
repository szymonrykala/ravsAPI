<?php
declare(strict_types=1);

namespace App\Domain\Request;

use App\Domain\Model\Model;
use App\Domain\User\User;

use DateTime;

Class Request extends Model{

    public string $method;
    public string $endpoint;
    public User $user;
    public array $payload;
    
    public int $userId;

    /**
     * @param int       $id,
     * @param string    $path,
     * @param string    $created,
     * @param string    $updated
     */
    public function __construct(
        int $id,
        string $method,
        string $endpoint,
        int $userId,
        string $payload,
        DateTime $created,
        DateTime $updated
    )
    {
        parent::__construct($id, $created, $updated);

        $this->method = $method;
        $this->endpoint = $endpoint;
        $this->userId = $userId;
        $this->payload = json_decode($payload, TRUE);
    }

    /**
     * @return array
     */
    public function jsonSerialize():array
    {
        return [
            "id" => $this->id,
            "method" => $this->method,
            "endpoint" => $this->endpoint,
            "user" => $this->user ?? $this->userId,
            "payload" => $this->payload,
            "created" => $this->created->format('c'),
            "updated" => $this->updated->format('c')
        ];
    }

}