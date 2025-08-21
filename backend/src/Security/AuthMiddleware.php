<?php
declare(strict_types=1);

namespace App\Security;

use App\Http\Request;
use App\Http\Response;

class AuthMiddleware
{
    public static function requireAuth(Request $req, Response $res): array
    {
        $authHeader = $req->headers['Authorization'] ?? $req->headers['authorization'] ?? '';
        if (!str_starts_with($authHeader, 'Bearer ')) {
            $res->json(['error' => 'Unauthorized'], 401);
        }
        $token = substr($authHeader, 7);
        try {
            $claims = (new JwtService())->verify($token);
            return $claims;
        } catch (\Throwable $e) {
            $res->json(['error' => 'Invalid token'], 401);
        }
    }

    public static function requireAdmin(array $claims, Response $res): void
    {
        if (($claims['role'] ?? '') !== 'admin') {
            $res->json(['error' => 'Forbidden'], 403);
        }
    }
}

