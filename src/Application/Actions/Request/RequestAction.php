<?php

declare(strict_types=1);

namespace App\Application\Actions\Request;

use App\Domain\Request\RequestRepositoryInterface;
use App\Domain\User\UserRepositoryInterface;
use App\Application\Actions\Action;
use Psr\Container\ContainerInterface;
use Psr\Log\LoggerInterface;


abstract class RequestAction extends Action
{
    protected RequestRepositoryInterface $requestRepository;
    protected UserRepositoryInterface $userRepository;


    public function __construct(ContainerInterface $di)
    {
        parent::__construct($di->get(LoggerInterface::class));
        $this->requestRepository = $di->get(RequestRepositoryInterface::class);
        $this->userRepository = $di->get(UserRepositoryInterface::class);
    }
}
