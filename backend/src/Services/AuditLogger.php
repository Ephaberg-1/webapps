<?php
declare(strict_types=1);

namespace App\Services;

use App\Database\Database;
use PDO;

class AuditLogger
{
    public static function log(?int $userId, string $action, array $details = []): void
    {
        $pdo = Database::pdo();
        $stmt = $pdo->prepare('INSERT INTO audit_logs (user_id, ip_address, action, details) VALUES (:user_id, :ip, :action, :details)');
        $stmt->execute([
            ':user_id' => $userId,
            ':ip' => $_SERVER['REMOTE_ADDR'] ?? null,
            ':action' => $action,
            ':details' => json_encode($details, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE),
        ]);
    }
}

