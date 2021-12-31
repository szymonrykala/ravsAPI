<?php

declare(strict_types=1);

namespace App\Domain\User;

use App\Domain\Image\Image;
use App\Domain\Access\Access;
use App\Domain\Model\Model;
use App\Domain\User\Exceptions\BadCredentialsException;
use App\Domain\User\Exceptions\DeletedUserUpdateException;
use App\Domain\User\Exceptions\InvalidUserCodeException;
use App\Domain\User\Exceptions\UserBlockedException;
use App\Domain\User\Exceptions\UserNotActivatedException;
use App\Utils\JsonDateTime;
use stdClass;



final class User extends Model
{
    private bool $metadataShouldBeLoaded = FALSE;

    public function __construct(
        public int $id,
        public string $name,
        public string $surname,
        public string $email,
        public string $password,
        public bool $activated,
        public int $loginFails,
        public bool $blocked,
        public bool $deleted,
        public ?string $uniqueKey,
        public JsonDateTime $lastGeneratedKeyDate,
        public ?Access $access,
        public Image $image,
        public stdClass $metadata,
        public JsonDateTime $lastActivity,
        public JsonDateTime $created,
        public JsonDateTime $updated,
        public int $imageId,
        public int $accessId
    ) {
        parent::__construct($id, $created, $updated);
    }

    /** 
     * generates unique key
     */
    public static function generateUniqueKey(int $length = 10): string
    {
        return strtoupper(substr(uniqid(), 0, $length));
    }


    /**
     * Asserts that the key provided by user is correct
     * @throws InvalidUserCodeException
     */
    private function assertUniqueKeyIsCorrect(string $userKey): void
    {
        if (empty($this->uniqueKey) || $userKey !== $this->uniqueKey) {
            throw new InvalidUserCodeException();
        }
    }

    /**
     * Assigns unique key to the user
     */
    public function assignUniqueKey(): void
    {
        $this->lastGeneratedKeyDate = new JsonDateTime('now');
        $this->uniqueKey = User::generateUniqueKey();
    }


    /**
     * Activates the user if provided key is correct
     */
    public function activate(string $userKey): void
    {
        $this->assertUniqueKeyIsCorrect($userKey);
        $this->activated = TRUE;
        $this->uniqueKey = "";
    }


    /**
     * Unblock the user if provided key is correct
     */
    public function unblock(string $userKey): void
    {
        $this->assertUniqueKeyIsCorrect($userKey);
        $this->blocked = FALSE;
        $this->loginFails = 0;
        $this->uniqueKey = "";
    }

    /**
     * Login the user.
     * Checks if the user is activated.
     * Authenticate the user and handle blocking in case of too many failed tries.
     * @throws UserBlockedException
     * @throws BadCredentialsException
     * @throws UserNotActivatedException
     */
    public function login(string $userPassword): void
    {

        if ($this->blocked === TRUE) {
            throw new UserBlockedException();
        }

        if (password_verify($userPassword, $this->password)) {
            $this->loginFails = 0;
        } else {
            $this->loginFails += 1;
            if ($this->loginFails > 3) {
                $this->blocked = TRUE;
            }
            throw new BadCredentialsException();
        }

        if (!$this->activated) {
            throw new UserNotActivatedException();
        }
    }

    /** 
     * Checks if user is the user who performing request
     */
    public function isSessionUser(stdClass $session): bool
    {
        return $this->id === $session->userId;
    }

    /**
     * Triggers loading metadata to User JSON representation
     */
    public function loadMetadata(): void
    {
        $this->metadataShouldBeLoaded = TRUE;
    }


    /**
     * {@inheritDoc}
     * @throws DeletedUserUpdateException
     */
    public function validate(): void
    {
        if ($this->deleted)
            throw new DeletedUserUpdateException();
    }

    /**
     * {@inheritDoc}
     */
    public function jsonSerialize(): array
    {
        $view = array_merge(
            [
                'email' => $this->email,
                'name' => $this->name,
                'surname' => $this->surname,
                'activated' => $this->activated,
                'deleted' => $this->deleted,
                'image' => $this->image,
                'access' => $this->access ?? $this->accessId,
                'lastActivity' => $this->lastActivity,
            ],
            parent::jsonSerialize()
        );

        if ($this->metadataShouldBeLoaded) $view["metadata"] = $this->metadata;

        return $view;
    }
}
