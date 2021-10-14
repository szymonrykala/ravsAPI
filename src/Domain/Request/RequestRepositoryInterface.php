<?php
declare(strict_types=1);

namespace App\Domain\Request;

use App\Domain\Model\RepositoryInterface;
use stdClass;


interface RequestRepositoryInterface extends RepositoryInterface
{
    /**
     * {@inheritDoc}
     */
    public function create(
        string $method, 
        string $uriPath, 
        ?int $userId, 
        stdClass $payload,
        float $time
    ): void;

    /**
     * Deletes list of requests specified in $ids param
     */
    public function deleteList(array $ids): void;

}