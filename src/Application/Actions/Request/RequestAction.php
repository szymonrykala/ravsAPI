<?php
declare(strict_types=1);

namespace App\Application\Actions\Request;

use App\Domain\Request\RequestRepositoryInterface;
use App\Domain\User\UserRepositoryInterface;
use App\Application\Actions\Action;
use Psr\Log\LoggerInterface;


abstract class RequestAction extends Action
{

    protected RequestRepositoryInterface $requestRepository;

    public function __construct(
        RequestRepositoryInterface $requestRepository,
        LoggerInterface $logger,
        UserRepositoryInterface $userRepository
    )
    {
        parent::__construct($logger);
        $this->requestRepository = $requestRepository;
        $this->userRepository = $userRepository;
    }
}