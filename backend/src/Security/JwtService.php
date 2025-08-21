<?php
declare(strict_types=1);

namespace App\Security;

use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use App\Config\Env;

class JwtService
{
    private string $secret;
    private int $accessTtl;

    public function __construct()
    {
        $this->secret = Env::get('JWT_SECRET', 'change_me');
        $this->accessTtl = (int) Env::get('JWT_ACCESS_TTL', '900');
    }

    public function issueAccessToken(int $userId, string $role): string
    {
        $now = time();
        $payload = [
            'iss' => Env::get('APP_URL', ''),
            'iat' => $now,
            'nbf' => $now,
            'exp' => $now + $this->accessTtl,
            'sub' => (string) $userId,
            'role' => $role,
        ];
        return JWT::encode($payload, $this->secret, 'HS256');
    }

    public function verify(string $jwt): array
    {
        $decoded = JWT::decode($jwt, new Key($this->secret, 'HS256'));
        return (array) $decoded;
    }
}

