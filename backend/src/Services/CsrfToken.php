<?php
declare(strict_types=1);

namespace App\Services;

use App\Config\Env;

class CsrfToken
{
    public static function issue(): string
    {
        $token = bin2hex(random_bytes(32));
        $cookieParams = [
            'expires' => time() + 3600,
            'path' => '/',
            'domain' => '',
            'secure' => true,
            'httponly' => false,
            'samesite' => 'Strict',
        ];
        setcookie('csrf_token', $token, $cookieParams);
        return $token;
    }

    public static function validate(string $headerToken): bool
    {
        $cookieToken = $_COOKIE['csrf_token'] ?? '';
        return hash_equals($cookieToken, $headerToken);
    }
}

