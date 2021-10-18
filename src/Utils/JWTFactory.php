<?php

declare(strict_types=1);

namespace App\Utils;

use \Firebase\JWT\{
    ExpiredException,
    JWT
};
use App\Domain\User\User;
use DateTime;
use RuntimeException;
use stdClass;


class JWTFactory
{
    private const DOMAIN_IDENTITY = "ravsapi.szymonr.pl";


    public static function generateToken(User $user): string
    {
        $now = new DateTime('now');
        $data = [
            'userId' => $user->id,
            'accessId' => $user->accessId,
            'iat' => $now->getTimestamp(),                        // timestamp token issuing
            'iss' => JWTFactory::DOMAIN_IDENTITY,                 // domain indentifier
            'exp' => $now->modify('+20 hours')->getTimestamp()    // expiration timestamp
        ];

        $secret = $_ENV['TOKEN_SECRET'];

        $jwt = JWT::encode(
            $data,
            $secret,
            'HS512'
        );

        if ($jwt !== false) {
            return $jwt;
        }

        throw new JWTGeneratorException();
    }

    public static function decode(string $userToken): stdClass
    {
        $secret = $_ENV['TOKEN_SECRET'];

        try {
            $token = JWT::decode(
                $userToken,
                $secret,
                ['HS512']
            );
        } catch (ExpiredException $e) {
            throw new TokenExpiredException();
        } catch (\Exception $e) {
            throw new TokenNotValidException();
        }

        $now = new DateTime('now');

        if (
            $now->getTimestamp() > $token->exp
        ) throw new TokenExpiredException();

        return $token;
    }
}

class JWTException extends RuntimeException
{
};

class JWTGeneratorException extends JWTException
{
    public $message = "Nie udało się wygenerować tokenu. Spróbuj jeszcze raz.";
}

class TokenExpiredException extends JWTException
{
    public $message = "Token wygasł. Zaloguj się ponownie.";
}

class TokenNotValidException extends JWTException
{
    public $message = "Token jest nieprawidłowy.";
}
