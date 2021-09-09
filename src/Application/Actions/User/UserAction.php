<?php
declare(strict_types=1);

namespace App\Application\Actions\User;

use App\Domain\User\User as User;
use Slim\Exception\HttpNotFoundException;

use App\Application\Actions\Action;
use App\Domain\User\UserRepositoryInterface;

use Psr\Log\LoggerInterface;


abstract class UserAction extends Action
{
    protected UserRepositoryInterface $userRepository; 

    /**
     * @param LoggerInterface $logger
     * @param UserRepository $userRepository
     */
    public function __construct(
        LoggerInterface $logger,
        UserRepositoryInterface $userRepository
    ) {
        
        parent::__construct($logger);
        $this->userRepository = $userRepository;
    }

    /**
     * @param string $email
     * @return User
     * 
     * @throws HttpNotFoundException
     */
    protected function getUserByEmail(string $email): User
    {
        $res = $this->userRepository->where(['email'=> $email])->all();
        $user = array_pop($res);


        if(empty($user)) throw new HttpNotFoundException(
            $this->request,
            "User '${email}' not exist."
        );

        return $user;
    }
}
