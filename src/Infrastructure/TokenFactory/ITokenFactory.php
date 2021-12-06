<?php

declare(strict_types=1);

namespace App\Infrastructure\TokenFactory;

use App\Domain\User\User;
use stdClass;



interface ITokenFactory
{
    /**
     * Creates token for specific $user
     */
    public function generateToken(User $user): string;

    /**
     * Decodes recieved token
     */
    public function decode(string $userToken): stdClass;
}
