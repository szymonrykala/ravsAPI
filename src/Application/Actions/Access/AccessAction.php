<?php
declare(strict_types=1);

namespace App\Application\Actions\Access;

use Psr\Log\LoggerInterface;
use App\Application\Actions\Action;
use App\Domain\Access\IAccessRepository;



abstract class AccessAction extends Action
{

    public function __construct(
        LoggerInterface $logger,
        protected IAccessRepository $accessRepository
    ) {
        parent::__construct($logger);
    }
}