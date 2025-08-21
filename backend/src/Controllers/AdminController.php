<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Http\Request;
use App\Http\Response;
use App\Security\AuthMiddleware;
use App\Services\Mailer;

class AdminController
{
    public function broadcast(Request $req, Response $res): void
    {
        $claims = AuthMiddleware::requireAuth($req, $res);
        AuthMiddleware::requireAdmin($claims, $res);
        $subject = trim((string) ($req->body['subject'] ?? ''));
        $body = (string) ($req->body['html'] ?? '');
        if ($subject === '' || $body === '') {
            $res->json(['error' => 'Invalid input'], 400);
        }
        $pdo = \App\Database\Database::pdo();
        $emails = $pdo->query('SELECT email FROM users')->fetchAll(\PDO::FETCH_COLUMN);
        $mailer = new Mailer();
        $sent = 0;
        foreach ($emails as $email) {
            if ($mailer->send($email, $subject, $body)) {
                $sent++;
            }
        }
        $res->json({ 'sent' => $sent });
    }
}

