<?php

namespace App\Services\Auth;

use App\Models\User;
use DateTimeImmutable;
use Lcobucci\JWT\Configuration;
use Lcobucci\JWT\Encoding\ChainedFormatter;
use Lcobucci\JWT\Encoding\JoseEncoder;
use Lcobucci\JWT\Signer\Key\InMemory;
use Lcobucci\JWT\Signer\Rsa\Sha256;
use Lcobucci\JWT\Token;
use Lcobucci\JWT\Token\Builder;
use Lcobucci\JWT\Token\InvalidTokenStructure;
use Lcobucci\JWT\Token\Parser;
use Lcobucci\JWT\UnencryptedToken;
use Lcobucci\JWT\Validation\Constraint\IssuedBy;
use Lcobucci\JWT\Validation\Constraint\PermittedFor;
use Lcobucci\JWT\Validation\Validator;

class JWTService
{
    /**
     * @param User $user
     * @return UnencryptedToken
     * @throws \Exception
     */
    public function issueToken(User $user): UnencryptedToken
    {
        $tokenBuilder = (new Builder(
            new JoseEncoder(),
            ChainedFormatter::default()
        ));

        $path = base_path('/key.pem');
        if ($path === '') {
            throw new \InvalidArgumentException('file path is empty');
        }
        if ( ! file_exists($path)) {
            throw new \InvalidArgumentException('file not found: ');
        }

        $configuration = Configuration::forAsymmetricSigner(
            new Sha256(),
            InMemory::file($path),
            InMemory::base64Encoded(config('jwt')['KEY'])
        );
        $now = new DateTimeImmutable();
        return $tokenBuilder
            ->issuedBy(config('app.url'))
            ->permittedFor(config('app.url'))
            ->identifiedBy((string) $user->id)
            ->issuedAt($now)
            ->expiresAt(
                $now->modify('+ ' . config('jwt')['TTL'] . ' seconds')
            )
            ->withClaim('uuid', $user->uuid)
            ->withClaim('role', $user->is_admin ? 'admin' : 'user')
            ->getToken($configuration->signer(), $configuration->signingKey());

    }

    /**
     * @param string $token
     * @return Token
     */
    public function parseToken(string $token): Token
    {
        if ($token === '') {
            throw new InvalidTokenStructure('Token cannot be empty');
        }
        $parser = new Parser(new JoseEncoder());

        return $parser->parse($token);
    }


    /**
     * @param UnencryptedToken $token
     * @return bool
     */
    public function validateToken(UnencryptedToken $token): bool
    {
        $validator = new Validator();
        $constraints = [
            new IssuedBy(config('app.url')),
            new PermittedFor(config('app.url')),
        ];

        return $validator->validate($token, ...$constraints);
    }
}
