<?php
declare(strict_types=1);

namespace App\Domain\User;

use App\Domain\Model\RepositoryInterface;



interface UserRepositoryInterface extends RepositoryInterface
{


    /**
     * @param User $user
     * @throws UserUpdateException
     */
    public function save(User $user): void;


    /**
     * @param string $name
     * @param string $surname
     * @param string $email
     * @param string $password
     * @return int
     * 
     * @throws IncorrectEmailException
     */
    public function register(
        string $name,
        string $surname,
        string $email,
        string $password
    ): int;

}
