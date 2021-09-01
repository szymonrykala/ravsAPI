<?php
declare(strict_types=1);

namespace App\Application\Actions\Access;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Log\LoggerInterface;
use App\Application\Actions\Action;
use App\Domain\Access\AccessRepositoryInterface;
use App\Domain\Request\RequestRepositoryInterface;


abstract class AccessAction extends Action
{
    protected AccessRepositoryInterface $accessRepository;

    /**
     * @param LoggerInterface $logger
     * @param AccessRepositoryInterface $imageRepository
     */
    public function __construct(
        LoggerInterface $logger,
        AccessRepositoryInterface $repository,
        RequestRepositoryInterface $requestRepo
    ) {
        parent::__construct($logger, $requestRepo);
        $this->accessRepository = $repository;
    }
}