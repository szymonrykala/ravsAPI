<?php
declare(strict_types=1);

namespace App\Application\Actions\User;

use App\Domain\User\User as User;
use Slim\Exception\HttpNotFoundException;

use App\Application\Actions\Action;
use App\Domain\User\UserRepositoryInterface;
use App\Domain\Access\AccessRepositoryInterface;
use App\Domain\Image\ImageRepositoryInterface;
use App\Domain\Request\RequestRepositoryInterface;

use Psr\Log\LoggerInterface;
use App\Application\Actions\IActionCache;


abstract class UserAction extends Action
{
    protected $userRepository;    
    protected $imageRepository;    
    protected $accessRepository;

    protected IActionCache $cache;

    /**
     * @param LoggerInterface $logger
     * @param UserRepository $userRepository
     * @param ImageRepositoryInterface $imageRepository
     * @param AccessRepositoryInterface $accessRepository
     */
    public function __construct(
        LoggerInterface $logger,
        RequestRepositoryInterface $requestRepo,
        IActionCache $cache,
        UserRepositoryInterface $userRepository,
        ImageRepositoryInterface $imageRepository,
        AccessRepositoryInterface $accessRepository
    ) {
        
        parent::__construct($logger, $requestRepo);
        $this->cache = $cache;
        $this->userRepository = $userRepository;
        $this->imageRepository = $imageRepository;
        $this->accessRepository = $accessRepository;
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
