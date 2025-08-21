<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Http\Request;
use App\Http\Response;
use App\Repositories\UserRepository;
use App\Security\AuthMiddleware;
use OTPHP\TOTP;

class TwoFactorController
{
    public function __construct(private readonly UserRepository $users = new UserRepository()) {}

    public function setup(Request $req, Response $res): void
    {
        $claims = AuthMiddleware::requireAuth($req, $res);
        $userId = (int) $claims['sub'];

        $user = $this->users->findById($userId);
        $secret = $user['totp_secret'] ?? '';
        if (!$secret) {
            $secret = TOTP::create()->getSecret();
            $this->users->setTwoFactor($userId, $secret, false);
        }
        $totp = TOTP::create($secret);
        $totp->setLabel($user['email'] ?? 'user');
        $otpauth = $totp->getProvisioningUri();
        $res->json(['secret' => $secret, 'otpauth' => $otpauth]);
    }

    public function enable(Request $req, Response $res): void
    {
        $claims = AuthMiddleware::requireAuth($req, $res);
        $userId = (int) $claims['sub'];
        $code = trim((string) ($req->body['code'] ?? ''));
        $user = $this->users->findById($userId);
        $secret = $user['totp_secret'] ?? '';
        if (!$secret || $code === '') {
            $res->json(['error' => 'Invalid request'], 400);
        }
        $totp = TOTP::create($secret);
        if (!$totp->verify($code)) {
            $res->json(['error' => 'Invalid code'], 400);
        }
        $this->users->setTwoFactor($userId, $secret, true);
        $res->json(['enabled' => true]);
    }

    public function verify(Request $req, Response $res): void
    {
        // Optional endpoint if doing a second step
        $claims = AuthMiddleware::requireAuth($req, $res);
        $userId = (int) $claims['sub'];
        $code = trim((string) ($req->body['code'] ?? ''));
        $user = $this->users->findById($userId);
        $secret = $user['totp_secret'] ?? '';
        if (!$secret || $code === '') {
            $res->json(['error' => 'Invalid request'], 400);
        }
        $totp = TOTP::create($secret);
        if (!$totp->verify($code)) {
            $res->json(['error' => 'Invalid code'], 400);
        }
        $res->json(['verified' => true]);
    }
}

