<?php

declare(strict_types=1);

namespace App\Infrastructure\Repository;

use App\Infrastructure\Repository\BaseRepository;

use App\Domain\Access\IAccessRepository;
use App\Domain\Image\IImageRepository;
use App\Domain\Model\Model;
use App\Domain\User\Exceptions\DefaultUserDeleteException;
use App\Domain\User\IUserRepository;
use App\Domain\User\User;
use App\Utils\JsonDateTime;
use Psr\Container\ContainerInterface;


final class UserRepository extends BaseRepository implements IUserRepository
{
    protected string $table = '"user"';
    private bool $accessLoading = FALSE;

    public function __construct(
        ContainerInterface $di,
        private IImageRepository $imageRepository,
        private IAccessRepository $accessRepository
    ) {
        parent::__construct($di);
    }

    /**
     * {@inheritDoc}
     */
    public function withAccess(): IUserRepository
    {
        $this->accessLoading = TRUE;
        return $this;
    }

    /**
     * {@inheritDoc}
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
            (bool)  $data['deleted'],
            $data['unique_key'],
            new JsonDateTime($data['last_generated_key_date']),
            $access,
            $image,
            json_decode($data['metadata']),
            new JsonDateTime($data['last_activity']),
            new JsonDateTime($data['created']),
            new JsonDateTime($data['updated']),
            (int)   $data['image'],
            (int)   $data['access']
        );
    }

    /**
     * {@inheritDoc}
     */
    public function registerActivity(int $userId): void
    {
        $this->db->query(
            "UPDATE $this->table SET last_activity = NOW() WHERE id = :userId",
            [':userId' => $userId]
        );
    }

    /**
     * {@inheritDoc}
     */
    public function setDefaultImage(User $user): void
    {
        $this->db->query(
            "UPDATE $this->table SET image = DEFAULT WHERE id = :id",
            [':id' => $user->id]
        );
    }

    /**
     * {@inheritDoc}
     */
    public function save(User $user): void
    {
        $user->validate();
        $sql = "UPDATE $this->table SET
                    name = :name,
                    surname = :surname,
                    password = :password,
                    activated = :activated,
                    login_fails = :loginFails,
                    blocked = :blocked,
                    unique_key = :uniqueKey,
                    last_generated_key_date = :lastGeneratedKeyDate,
                    access = :accessId,
                    image = :imageId,
                    metadata = :metadata
                WHERE id = :id";

        $params = [
            ':id' => $user->id,
            ':name' => ucfirst($user->name),
            ':surname' => ucfirst($user->surname),
            ':password' => $user->password,
            ':activated' => $user->activated,
            ':loginFails' => $user->loginFails,
            ':blocked' => $user->blocked,
            ':uniqueKey' => $user->uniqueKey,
            ':lastGeneratedKeyDate' => $user->lastGeneratedKeyDate->format('Y-m-d H:i:s'),
            ':accessId' => $user->accessId,
            ':imageId' => $user->imageId,
            ':metadata' => json_encode($user->metadata),
        ];
        $this->db->query($sql, $params);
    }


    /**
     * {@inheritDoc}
     */
    public function register(
        string $name,
        string $surname,
        string $email,
        string $password
    ): int {

        $sql = "INSERT INTO $this->table
                        (name, surname, email, password,unique_key, image, access) 
                VALUES (:name, :surname, :email, :password, :uniqueKey, DEFAULT,
                    (SELECT value FROM $this->configTable WHERE key='DEFAULT_USER_ACCESS')
                )";

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


    /**
     * {@inheritDoc}
     */
    public function search(string $phrase): UserRepository
    {
        $this->SQLwhere = " WHERE 
                        (email LIKE :phrase
                        OR name LIKE :phrase
                        OR surname LIKE :phrase) ";
        $this->params[':phrase'] = '%' . str_replace(' ', '%', $phrase) . '%';

        return $this;
    }

    /**
     * If used twice on same user, will casuse permanently deletion of the user and all resources related to it.
     * Sets default iamge to the user and deletes used previously
     * {@inheritDoc}
     * @param User $user
     * @throws DefaultUserDeleteException
     */
    public function delete(Model $user): void
    {
        // cannot delete user with predefined access class
        if ($user->accessId === 1) throw new DefaultUserDeleteException();

        if ($user->deleted) {
            //permanently deleting
            parent::delete($user);
            return;
        }

        $user->setAsDeleted();


        $sql = "UPDATE $this->table SET
                    name = :name,
                    surname = :surname,
                    deleted = :deleted,
                    email = :email,
                    image = DEFAULT
                WHERE id = :id";

        $params = [
            ':id' => $user->id,
            ':name' => $user->name,
            ':surname' => $user->surname,
            ':deleted' => $user->deleted,
            ':email' => $user->email
        ];

        $this->db->query($sql, $params);

        // deleting image
        $this->imageRepository->delete($user->image);
    }
}
