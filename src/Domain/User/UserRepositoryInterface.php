<?php
declare(strict_types=1);

namespace App\Domain\User;

use App\Domain\Model\RepositoryInterface;



interface UserRepositoryInterface extends RepositoryInterface
{

    /**
     * enables loading of user access properties
     * @return UserRepositoryInterface
     */
    public function withAccess(): UserRepositoryInterface;

    /**
     * saves state of the user
     * @throws UserUpdateException
     */
    public function save(User $user): void;


    /**
     * Creates new User account
     * @throws IncorrectEmailException
     */
    public function register(
        string $name,
        string $surname,
        string $email,
        string $password
    ): int;


    /**
     * search for a users with given phrase by name, surname, and email
     */
    public function search(string $phrase): UserRepositoryInterface;

}
