<?php
declare(strict_types=1);

namespace App\Domain\Request;

use App\Domain\Model\RepositoryInterface;
use Psr\Http\Message\ServerRequestInterface;
use stdClass;


interface RequestRepositoryInterface extends RepositoryInterface
{
    /**
     * {@inheritDoc}
     */
    public function create(ServerRequestInterface $request): void;

    /**
     * Deletes list of requests specified in $ids param
     */
    public function deleteList(array $ids): void;

}