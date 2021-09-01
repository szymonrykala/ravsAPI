<?php
declare(strict_types=1);

namespace App\Utils;

use App\Domain\Exception\DomainUnauthorizedOperationException;

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

    private const KEY = 'bGS6lzFqvvSQ8ALbOxatm7/Vk7mLQyzqaS34Q4oR1ew=';

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

        $jwt = JWT::encode(
            $data,    
            JWTFactory::KEY,
            'HS512'
        );

        if($jwt !== false){
            return $jwt;
        }

        throw new JWTGeneratorException();
    }

    public static function decode(string $userToken): stdClass
    {
        try{

            $token = JWT::decode(
                $userToken,
                JWTFactory::KEY,
                ['HS512']
            );
        }catch(ExpiredException $e){
            throw new TokenExpiredException(); 
        }catch(\Exception $e){
            throw new TokenNotValidException();
        }
        
        $now = new DateTime('now');

        if(
            $now->getTimestamp() > $token->exp
        ) throw new TokenExpiredException();

        return $token;
    }
}


class JWTGeneratorException extends RuntimeException
{
    public $message = "Could not generate an auth token. Please try again";
}

class TokenExpiredException extends DomainUnauthorizedOperationException
{
    public $message = "Token has expired. Login to get new one.";
}

class TokenNotValidException extends DomainUnauthorizedOperationException
{
    public $message = "Token is invalid.";
}