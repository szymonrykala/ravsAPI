<?php

declare(strict_types=1);

namespace App\Domain\User;

use App\Domain\Model\IRepository;



interface IUserRepository extends IRepository
{

    /**
     * enables loading of user access properties
     * @return IUserRepository
     */
    public function withAccess(): IUserRepository;

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
     * Register activity for user with specified userId
     */
    public function registerActivity(int $userId): void;

    /**
     * search for a users with given phrase by name, surname, and email
     */
    public function search(string $phrase): IUserRepository;
}
