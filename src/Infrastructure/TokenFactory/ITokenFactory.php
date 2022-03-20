<?php

declare(strict_types=1);

namespace App\Infrastructure\TokenFactory;

use App\Domain\User\User;
use stdClass;



interface ITokenFactory
{
    /**
     * Creates token for specific $user
     * @throws TokenFactoryException
     */
    public function generateToken(User $user): string;

    /**
     * Decodes recieved token
     * @throws TokenExpiredException
     * @throws TokenNotValidException
     */
    public function decode(string $userToken): stdClass;
}
