<?php
declare(strict_types=1);

namespace App\Domain\Request;

use App\Domain\Model\IRepository;
use Psr\Http\Message\ServerRequestInterface;


interface IRequestRepository extends IRepository
{
    /**
     * Creates HTTP request log
     */
    public function create(ServerRequestInterface $request): void;


    /**
     * search entires where search params are LIKE given values
     */
    public function whereLIKE(array $searchParams): IRequestRepository;
}