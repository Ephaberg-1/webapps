<?php
declare(strict_types=1);

namespace App\Security;

use App\Database\Database;
use PDO;

class RateLimiter
{
    public function __construct(private readonly string $key, private readonly int $max, private readonly int $windowSeconds)
    {
    }

    public function allow(): bool
    {
        $pdo = Database::pdo();
        $keyHash = hash('sha256', $this->key);
        $now = time();
        $windowStart = date('Y-m-d H:i:s', $now - $this->windowSeconds);

        $pdo->beginTransaction();
        try {
            $stmt = $pdo->prepare('SELECT id, count, window_start FROM rate_limits WHERE key_hash = :key_hash FOR UPDATE');
            $stmt->execute([':key_hash' => $keyHash]);
            $row = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($row) {
                if ($row['window_start'] < $windowStart) {
                    $stmt = $pdo->prepare('UPDATE rate_limits SET window_start = NOW(), count = 1 WHERE id = :id');
                    $stmt->execute([':id' => $row['id']]);
                    $pdo->commit();
                    return true;
                }
                if ((int) $row['count'] >= $this->max) {
                    $pdo->commit();
                    return false;
                }
                $stmt = $pdo->prepare('UPDATE rate_limits SET count = count + 1 WHERE id = :id');
                $stmt->execute([':id' => $row['id']]);
                $pdo->commit();
                return true;
            } else {
                $stmt = $pdo->prepare('INSERT INTO rate_limits (key_hash, window_start, count) VALUES (:key_hash, NOW(), 1)');
                $stmt->execute([':key_hash' => $keyHash]);
                $pdo->commit();
                return true;
            }
        } catch (\Throwable $e) {
            $pdo->rollBack();
            return false;
        }
    }
}

