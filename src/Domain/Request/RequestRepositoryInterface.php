<?php
declare(strict_types=1);

namespace App\Domain\Request;

use App\Domain\Model\RepositoryInterface;
use stdClass;


interface RequestRepositoryInterface extends RepositoryInterface
{
    /**
     * @param string $method
     * @param string $uriPath
     * @param int $userId
     * @param stdClass $payload
     * @return void
     */
    public function create(
        string $method, 
        string $uriPath, 
        int $userId, 
        stdClass $payload
    ): void;

    /**
     * @param array $id's
     * @return void
     */
    public function deleteList(array $ids): void;

}