<?php
declare(strict_types=1);

namespace App\Repositories;

use App\Database\Database;
use PDO;

class RefreshTokenRepository
{
    public function create(int $userId, string $tokenPlain, \DateTimeImmutable $expiresAt): void
    {
        $pdo = Database::pdo();
        $hash = hash('sha256', $tokenPlain);
        $stmt = $pdo->prepare('INSERT INTO refresh_tokens (user_id, token_hash, expires_at, revoked) VALUES (:user_id, :token_hash, :expires_at, 0)');
        $stmt->execute([
            ':user_id' => $userId,
            ':token_hash' => $hash,
            ':expires_at' => $expiresAt->format('Y-m-d H:i:s'),
        ]);
    }

    public function verifyAndConsume(int $userId, string $tokenPlain): bool
    {
        $pdo = Database::pdo();
        $hash = hash('sha256', $tokenPlain);
        $pdo->beginTransaction();
        try {
            $stmt = $pdo->prepare('SELECT id, expires_at, revoked FROM refresh_tokens WHERE user_id = :user_id AND token_hash = :hash FOR UPDATE');
            $stmt->execute([':user_id' => $userId, ':hash' => $hash]);
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            if (!$row) {
                $pdo->commit();
                return false;
            }
            if ((int) $row['revoked'] === 1 || strtotime($row['expires_at']) < time()) {
                $pdo->commit();
                return false;
            }
            $stmt = $pdo->prepare('UPDATE refresh_tokens SET revoked = 1 WHERE id = :id');
            $stmt->execute([':id' => $row['id']]);
            $pdo->commit();
            return true;
        } catch (\Throwable $e) {
            $pdo->rollBack();
            return false;
        }
    }
}

