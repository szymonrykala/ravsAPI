<?php
declare(strict_types=1);

namespace App\Application\Actions\Access;

use Psr\Log\LoggerInterface;
use App\Application\Actions\Action;
use App\Domain\Access\IAccessRepository;



abstract class AccessAction extends Action
{
    protected IAccessRepository $accessRepository;

    /**
     * @param LoggerInterface $logger
     * @param IAccessRepository $imageRepository
     */
    public function __construct(
        LoggerInterface $logger,
        IAccessRepository $repository
    ) {
        parent::__construct($logger);
        $this->accessRepository = $repository;
    }
}