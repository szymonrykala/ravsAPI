<?php

declare(strict_types=1);

namespace App\Domain\User;

use App\Domain\Image\Image;
use App\Domain\Access\Access;
use App\Domain\Model\Model;
use DateTime;
use stdClass;


class User extends Model
{
    public int $id;
    public string $name;
    public string $surname;
    public string $password;
    public string $email;
    public bool $activated;
    public int $loginFails;
    public bool $blocked;
    public string $uniqueKey;
    public DateTime $lastGeneratedKeyDate;
    public ?stdClass $metadata;

    public ?Access $access;
    public Image $image;

    public int $imageId;
    public int $accessId;

    public DateTime $created;
    public DateTime $updated;

    private bool $metadataShouldBeLoaded = FALSE;

    /**
     * @param int       id
     * @param string    name
     * @param string    surame
     * @param string    email
     * @param string    password
     * @param bool      activated
     * @param int       loginFails
     * @param bool      blocked
     * @param string    uniqueKey
     * @param DateTime    lastGeneratedKeyDate,
     * @param Access|NULL access
     * @param Image       image
     * @param stdClass    metadata
     * @param DateTime    created
     * @param DateTime    updated
     * @param int       imageId
     * @param int       accessId
     */
    public function __construct(
        int $id,
        string $name,
        string $surname,
        string $email,
        string $password,
        bool $activated,
        int $loginFails,
        bool $blocked,
        string $uniqueKey,
        DateTime $lastGeneratedKeyDate,
        ?Access $access,
        Image $image,
        stdClass $metadata,
        DateTime $created,
        DateTime $updated,
        int $imageId,
        int $accessId
    ) {
        parent::__construct($id, $created, $updated);

        $this->name = ucfirst($name);
        $this->surname = ucfirst($surname);
        $this->email = strtolower($email);
        $this->password = $password;
        $this->activated = $activated;
        $this->loginFails = $loginFails;
        $this->blocked = $blocked;
        $this->uniqueKey = $uniqueKey;
        $this->lastGeneratedKeyDate = $lastGeneratedKeyDate;
        
        $this->image = $image;
        $this->access = $access;

        $this->metadata = $metadata;

        $this->imageId = $imageId;
        $this->accessId = $accessId;

    }

    /** 
     * @param int length of the key
     * @return string unique key
     */
    public static function generateUniqueKey(int $length = 8): string
    {
        return strtoupper(substr(uniqid(), 0, $length));
    }


    /**
     * @param string $userUniqueKey
     * @throws InvalidUserCodeException
     */
    private function assertUniqueKeyIsCorrect(string $userKey): void
    {
        if (empty($this->uniqueKey) || $userKey !== $this->uniqueKey) {
            throw new Exceptions\InvalidUserCodeException();
        }
    }

    /**
     * @return DateTime $lastGeneratedKeyDate
     */
    public function getLastGeneratedKeyDate(): DateTime
    {
        return $this->lastGeneratedKeyDate;
    }


    /**
     * @return void
     */
    public function assignUniqueKey(int $length = 8): void
    {
        $this->lastGeneratedKeyDate = new DateTime('now');
        $this->uniqueKey = User::generateUniqueKey($length);
    }


    /**
     * @param string $userKey
     */
    public function activate(string $userKey): void
    {
        $this->assertUniqueKeyIsCorrect($userKey);
        $this->activated = TRUE;
        $this->uniqueKey = "";
    }


    /**
     * @param string $useKey
     */
    public function unblock(string $userKey): void
    {
        $this->assertUniqueKeyIsCorrect($userKey);
        $this->blocked = FALSE;
        $this->loginFails = 0;
        $this->uniqueKey = "";
    }

    /**
     * @param string $userPassword
     * @return void
     */
    public function login(string $userPassword): void
    {

        if ($this->blocked === TRUE) {
            throw new Exceptions\UserBlockedException();
        }

        if (password_verify($userPassword, $this->password)) {
            $this->loginFails = 0;
        } else {
            $this->loginFails += 1;
            if ($this->loginFails > 3) {
                $this->blocked = TRUE;
            }
            throw new Exceptions\BadCredentialsException();
        }

        if (!$this->activated) {
            throw new Exceptions\UserNotActivatedException();
        }
    }

    public function isSessionUser(stdClass $session): bool
    {
        return $this->id === $session->userId;
    }

    public function loadMetadata(): void
    {
        $this->metadataShouldBeLoaded = TRUE;
    }

    /**
     * @return array
     */
    public function jsonSerialize(): array
    {
        $view = [
            'id' => $this->id,
            'email' => $this->email,
            'name' => $this->name,
            'surname' => $this->surname,
            'activated' => $this->activated,
            'image' => $this->image,
            'access' => $this->access ?? $this->accessId,
            "created" => $this->created->format('c'),
            "updated" => $this->updated->format('c'),
        ];
        if ($this->metadataShouldBeLoaded) $view["metadata"] = $this->metadata;

        return $view;
    }
}
