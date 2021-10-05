<?php

declare(strict_types=1);

namespace App\Application\Actions\User;

use App\Domain\User\User as User;
use Slim\Exception\HttpNotFoundException;

use App\Application\Actions\Action;
use App\Domain\User\UserRepositoryInterface;
use Psr\Container\ContainerInterface;
use Psr\Log\LoggerInterface;


abstract class UserAction extends Action
{
    protected UserRepositoryInterface $userRepository;


    public function __construct(ContainerInterface $di)
    {
        parent::__construct($di->get(LoggerInterface::class));
        $this->userRepository = $di->get(UserRepositoryInterface::class);
    }

    /**
     * get user by email string 
     */
    protected function getUserByEmail(string $email): User
    {
        $user = $this->userRepository
            ->where(['email' => $email])
            ->one();


        return $user;
    }
}
