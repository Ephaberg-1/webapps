<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Http\Request;
use App\Http\Response;
use App\Repositories\UserRepository;
use App\Repositories\RefreshTokenRepository;
use App\Security\PasswordPolicy;
use App\Security\JwtService;
use App\Security\RecaptchaService;
use App\Security\RateLimiter;
use App\Config\Env;
use App\Services\AuditLogger;

class AuthController
{
    public function __construct(
        private readonly UserRepository $users = new UserRepository(),
        private readonly RefreshTokenRepository $refreshTokens = new RefreshTokenRepository(),
        private readonly JwtService $jwt = new JwtService(),
        private readonly RecaptchaService $recaptcha = new RecaptchaService()
    ) {
    }

    public function register(Request $req, Response $res): void
    {
        $ip = $_SERVER['REMOTE_ADDR'] ?? '';
        $limiter = new RateLimiter('register:' . $ip, 10, 900);
        if (!$limiter->allow()) {
            $res->json(['error' => 'Too Many Requests'], 429);
        }

        $email = filter_var($req->body['email'] ?? '', FILTER_VALIDATE_EMAIL);
        $password = (string) ($req->body['password'] ?? '');
        $fullName = trim((string) ($req->body['full_name'] ?? ''));
        $captcha = (string) ($req->body['recaptcha_token'] ?? '');

        if (!$email || $fullName === '' || $captcha === '') {
            $res->json(['error' => 'Invalid input'], 400);
        }
        if (!$this->recaptcha->verify($captcha, $ip)) {
            $res->json(['error' => 'reCAPTCHA failed'], 400);
        }
        $errors = PasswordPolicy::validate($password);
        if (!empty($errors)) {
            $res->json(['error' => 'Weak password', 'details' => $errors], 400);
        }
        if ($this->users->findByEmail($email)) {
            $res->json(['error' => 'Email already in use'], 409);
        }

        $hash = password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]);
        $userId = $this->users->create($email, $hash, $fullName);
        AuditLogger::log($userId, 'user.register', ['email' => $email]);

        $access = $this->jwt->issueAccessToken($userId, 'user');
        $refreshPlain = bin2hex(random_bytes(32));
        $refreshTtl = (int) Env::get('JWT_REFRESH_TTL', '1209600');
        $this->refreshTokens->create($userId, $refreshPlain, new \DateTimeImmutable('+' . $refreshTtl . ' seconds'));

        $res->json([
            'access_token' => $access,
            'refresh_token' => $refreshPlain,
            'user' => [
                'id' => $userId,
                'email' => $email,
                'full_name' => $fullName,
                'role' => 'user',
            ]
        ], 201);
    }

    public function login(Request $req, Response $res): void
    {
        $ip = $_SERVER['REMOTE_ADDR'] ?? '';
        $email = filter_var($req->body['email'] ?? '', FILTER_VALIDATE_EMAIL);
        $password = (string) ($req->body['password'] ?? '');
        $captcha = (string) ($req->body['recaptcha_token'] ?? '');

        $limiter = new RateLimiter('login:' . ($email ?: $ip), 5, 300);
        if (!$limiter->allow()) {
            $res->json(['error' => 'Too Many Requests'], 429);
        }
        if (!$email || $password === '' || $captcha === '') {
            $res->json(['error' => 'Invalid input'], 400);
        }
        if (!$this->recaptcha->verify($captcha, $ip)) {
            $res->json(['error' => 'reCAPTCHA failed'], 400);
        }
        $user = $this->users->findByEmail($email);
        if (!$user || !password_verify($password, $user['password_hash'])) {
            AuditLogger::log(null, 'auth.login_failed', ['email' => $email, 'ip' => $ip]);
            $res->json(['error' => 'Invalid credentials'], 401);
        }

        // If 2FA enabled, expect a second step (handled in future endpoint)
        $access = $this->jwt->issueAccessToken((int) $user['id'], $user['role']);
        $refreshPlain = bin2hex(random_bytes(32));
        $refreshTtl = (int) Env::get('JWT_REFRESH_TTL', '1209600');
        $this->refreshTokens->create((int) $user['id'], $refreshPlain, new \DateTimeImmutable('+' . $refreshTtl . ' seconds'));
        AuditLogger::log((int) $user['id'], 'auth.login_success', ['email' => $email]);

        $res->json([
            'access_token' => $access,
            'refresh_token' => $refreshPlain,
            'user' => [
                'id' => (int) $user['id'],
                'email' => $user['email'],
                'full_name' => $user['full_name'],
                'role' => $user['role'],
                'is_2fa_enabled' => (bool) $user['is_2fa_enabled'],
            ]
        ]);
    }

    public function refresh(Request $req, Response $res): void
    {
        $userId = (int) ($req->body['user_id'] ?? 0);
        $refreshToken = (string) ($req->body['refresh_token'] ?? '');
        if ($userId <= 0 || $refreshToken === '') {
            $res->json(['error' => 'Invalid input'], 400);
        }
        if (!$this->refreshTokens->verifyAndConsume($userId, $refreshToken)) {
            $res->json(['error' => 'Invalid refresh token'], 401);
        }
        $user = $this->users->findById($userId);
        if (!$user) {
            $res->json(['error' => 'User not found'], 404);
        }
        $access = $this->jwt->issueAccessToken($userId, $user['role']);
        $newRefresh = bin2hex(random_bytes(32));
        $refreshTtl = (int) Env::get('JWT_REFRESH_TTL', '1209600');
        $this->refreshTokens->create($userId, $newRefresh, new \DateTimeImmutable('+' . $refreshTtl . ' seconds'));
        AuditLogger::log($userId, 'auth.refresh', []);
        $res->json(['access_token' => $access, 'refresh_token' => $newRefresh]);
    }

    public function logout(Request $req, Response $res): void
    {
        $userId = (int) ($req->body['user_id'] ?? 0);
        $refreshToken = (string) ($req->body['refresh_token'] ?? '');
        if ($userId > 0 && $refreshToken !== '') {
            // Best-effort consume to revoke
            $this->refreshTokens->verifyAndConsume($userId, $refreshToken);
            AuditLogger::log($userId, 'auth.logout', []);
        }
        $res->json(['ok' => true]);
    }
}

