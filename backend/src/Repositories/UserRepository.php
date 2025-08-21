<?php
declare(strict_types=1);

namespace App\Repositories;

use App\Database\Database;
use PDO;

class UserRepository
{
    public function findByEmail(string $email): ?array
    {
        $pdo = Database::pdo();
        $stmt = $pdo->prepare('SELECT * FROM users WHERE email = :email');
        $stmt->execute([':email' => $email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        return $user ?: null;
    }

    public function findById(int $id): ?array
    {
        $pdo = Database::pdo();
        $stmt = $pdo->prepare('SELECT * FROM users WHERE id = :id');
        $stmt->execute([':id' => $id]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        return $user ?: null;
    }

    public function create(string $email, string $passwordHash, string $fullName, string $role = 'user'): int
    {
        $pdo = Database::pdo();
        $stmt = $pdo->prepare('INSERT INTO users (uuid, email, password_hash, full_name, role) VALUES (UUID(), :email, :password_hash, :full_name, :role)');
        $stmt->execute([
            ':email' => $email,
            ':password_hash' => $passwordHash,
            ':full_name' => $fullName,
            ':role' => $role,
        ]);
        return (int) $pdo->lastInsertId();
    }

    public function setTwoFactor(int $userId, ?string $totpSecret, bool $enabled): void
    {
        $pdo = Database::pdo();
        $stmt = $pdo->prepare('UPDATE users SET totp_secret = :secret, is_2fa_enabled = :enabled WHERE id = :id');
        $stmt->execute([
            ':secret' => $totpSecret,
            ':enabled' => $enabled ? 1 : 0,
            ':id' => $userId,
        ]);
    }
}

