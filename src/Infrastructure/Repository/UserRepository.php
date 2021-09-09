<?php

declare(strict_types=1);

namespace App\Infrastructure\Repository;

use App\Infrastructure\Repository\BaseRepository;

use App\Domain\Access\AccessRepositoryInterface;
use App\Domain\Image\ImageRepositoryInterface;
use App\Domain\User\UserRepositoryInterface;
use App\Domain\User\User;
use App\Infrastructure\Database\IDatabase;
use DateTime;


class UserRepository extends BaseRepository implements UserRepositoryInterface
{
    protected string $table = 'user';

    private ImageRepositoryInterface $imageRepository;
    private AccessRepositoryInterface $accessRepository;

    private bool $accessLoading = FALSE;


    /**
     * @param IDatabase db database
     * @param ImageInterfaceRepository imagerRpository
     */
    public function __construct(
        IDatabase $db,
        ImageRepositoryInterface $imageRepository,
        AccessRepositoryInterface $accessRepository
    ) {
        parent::__construct($db);
        $this->imageRepository = $imageRepository;
        $this->accessRepository = $accessRepository;
    }

    /**
     * {@inheritdoc}
     */
    public function withAccess(): UserRepositoryInterface
    {
        $this->accessLoading = TRUE;
        return $this;
    }

    /**
     * @param array $data from database
     * @return User
     */
    protected function newItem(array $data): User
    {
        $image = $this->imageRepository->byId((int)$data['image']);
        $access = $this->accessLoading ? $this->accessRepository->byId((int) $data['access']) : NULL;
        
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
            $access,
            $image,
            json_decode($data['metadata']),
            new DateTime($data['created']),
            new DateTime($data['updated']),
            (int)   $data['image'],
            (int)   $data['access']
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
    ): int {


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
