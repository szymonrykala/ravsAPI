<?php

declare(strict_types=1);

namespace App\Domain\Model;

use App\Utils\Pagination;


interface IRepository
{

    /**
     * Builds WHERE clause of query with provided data of array
     */
    public function where(array $searchParams): IRepository;

    /**
     * Enables ordering on results
     */
    public function orderBy(string $name, string $direction = 'DESC'): IRepository;

    /**
     * Sets pagination feature enabled
     */
    public function setPagination(Pagination &$pagination): IRepository;

    /**
     * reads all results of query
     * @return Model[]
     */
    public function all(): array;

    /**
     * gets one item from collection
     * @throws DomainRecordNotFoundException
     */
    public function one(): Model;

    /**
     * Reads specific object by provided id
     * @throws DomainRecordNotFoundException
     */
    public function byId(int $id): Model;

    /**
     * Deletes provided object
     */
    public function delete(Model $object): void;
}
