<?php
declare(strict_types=1);

namespace App\Infrastructure\Repository;

use App\Infrastructure\Repository\BaseRepository;
use App\Domain\User\UserRepositoryInterface;
use App\Domain\User\User;
use DateTime;


class UserRepository extends BaseRepository implements UserRepositoryInterface
{
    protected string $table = 'user';


    /**
     * @param array $data from database
     * @return User
     */
    protected function newItem(array $data): User
    {
        return new User(
            (int)   $data['id'],
                    $data['name'],
                    $data['surname'],
                    $data['email'],
                    $data['password'],
            (bool)  $data['activated'],
            (int)   $data['login_fails'],
            (bool)  $data['blocked'],
                    $data['unique_key'],
                    new DateTime($data['last_generated_key_date']),
            (int)   $data['access'],
            (int)   $data['image'],
                    json_decode($data['metadata']),
                    new DateTime($data['created']),
                    new DateTime($data['updated'])
        );
    }


    /**
     * {@inheritdoc}
     */
    public function save(User $user): void
    {
        $user->validate();
        $sql = "UPDATE `$this->table` SET
                    `name` = :name,
                    `surname` = :surname,
                    `password` = :password,
                    `activated` = :activated,
                    `login_fails` = :loginFails,
                    `blocked` = :blocked,
                    `unique_key` = :uniqueKey,
                    `last_generated_key_date` = :lastGeneratedKeyDate,
                    `access` = :accessId,
                    `image` = :imageId,
                    `metadata` = :metadata
                WHERE `id` = :id";

        $params = [
            ':id' => $user->id,
            ':name' => ucfirst($user->name),
            ':surname' => ucfirst($user->surname),
            ':password' => $user->password,
            ':activated' => (int) $user->activated,
            ':loginFails' => $user->loginFails,
            ':blocked' => (int) $user->blocked,
            ':uniqueKey' => $user->uniqueKey,
            ':lastGeneratedKeyDate' => $user->lastGeneratedKeyDate->format('Y-m-d H:i:s'),
            ':accessId' => $user->accessId,
            ':imageId' => $user->imageId,
            ':metadata' => json_encode($user->metadata),
        ];
        $this->db->query($sql, $params);
    }


    /**
     * {@inheritdoc}
     */
    public function register(
        string $name,
        string $surname,
        string $email,
        string $password
    ): int
    {


        $sql = "INSERT `$this->table`
                        (`name`, `surname`, `email`, `password`,`unique_key`) 
                VALUES (:name, :surname, :email, :password, :uniqueKey)";

        $params = [
            ':name' => ucfirst($name),
            ':surname' => ucfirst($surname),
            ':email' => $email,
            ':password' => password_hash($password, PASSWORD_BCRYPT),
            ':uniqueKey' => User::generateUniqueKey(),
        ];

        $this->db->query($sql, $params);
        return $this->db->lastInsertId();
    }

}
