<?php

declare(strict_types=1);

namespace App\Infrastructure\TokenFactory;

use App\Application\Settings\SettingsInterface;
use \Firebase\JWT\{
    ExpiredException,
    JWT
};
use App\Domain\User\User;
use DateTime;
use stdClass;
use App\Infrastructure\TokenFactory\Exceptions\{
    TokenFactoryException,
    TokenNotValidException,
    TokenExpiredException
};


class JWTFactory implements ITokenFactory
{
    private string $domain_identity;
    private object $settings;


    public function __construct(
        SettingsInterface $settings
    ) {
        $this->domain_identity = $_SERVER['SERVER_NAME'];
        $this->settings = (object) $settings->get('token');
    }

    public function generateToken(User $user): string
    {
        $now = new DateTime('now');
        $data = [
            'userId' => $user->id,
            'accessId' => $user->accessId,
            'iat' => $now->getTimestamp(),                        // timestamp token issuing
            'iss' => $this->domain_identity,                 // domain indentifier
            'exp' => $now->modify("+".$this->settings->expiry)->getTimestamp()    // expiration timestamp
        ];

        $jwt = JWT::encode(
            $data,
            $this->settings->secret,
            $this->settings->encoding
        );

        if ($jwt !== false) {
            return $jwt;
        }

        throw new TokenFactoryException();
    }

    public function decode(string $userToken): stdClass
    {
        try {
            $token = JWT::decode(
                $userToken,
                $this->settings->secret,
                [$this->settings->encoding]
            );
        } catch (ExpiredException $e) {
            echo $e->getMessage();
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
