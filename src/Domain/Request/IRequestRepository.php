<?php
declare(strict_types=1);

namespace App\Domain\Request;

use App\Domain\Model\IRepository;
use Psr\Http\Message\ServerRequestInterface;


interface IRequestRepository extends IRepository
{
    /**
     * {@inheritDoc}
     */
    public function create(ServerRequestInterface $request): void;

}