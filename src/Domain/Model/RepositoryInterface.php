<?php
declare(strict_types=1);

namespace App\Domain\Model;


interface RepositoryInterface
{

    /**
     * @param array $searchParams
     * @return self
     */
    public function where(array $searchParams): self;

    /**
     * @param array $dateParams
     * @return self
     */
    public function withDates(array $dateParams): self;
    
    /**
     * @param int $number
     * @param int $limit
     * @return array
     */
    public function page(int $number, int $limit): array;

    /**
     * @return Model[]
     */
    public function all(): array;

    /**
     * @param int $id
     * @return Model
     * @throws DomainRecordNotFoundException
     */
    public function byId(int $id): Model;

    /**
     * @param Model $object
     * @return void
     */
    public function delete(Model $object): void;
}