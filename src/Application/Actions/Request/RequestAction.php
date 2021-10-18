<?php

declare(strict_types=1);

namespace App\Application\Actions\Request;

use App\Domain\Request\IRequestRepository;
use App\Domain\User\IUserRepository;
use App\Application\Actions\Action;
use Psr\Container\ContainerInterface;
use Psr\Log\LoggerInterface;


abstract class RequestAction extends Action
{
    protected IRequestRepository $requestRepository;
    protected IUserRepository $userRepository;


    public function __construct(ContainerInterface $di)
    {
        parent::__construct($di->get(LoggerInterface::class));
        $this->requestRepository = $di->get(IRequestRepository::class);
        $this->userRepository = $di->get(IUserRepository::class);
    }
}
