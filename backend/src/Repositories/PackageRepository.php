<?php
declare(strict_types=1);

namespace App\Repositories;

use App\Database\Database;
use PDO;

class PackageRepository
{
    public function listActive(): array
    {
        $pdo = Database::pdo();
        $stmt = $pdo->query('SELECT * FROM packages WHERE is_active = 1 ORDER BY price_cents ASC');
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function upsert(?int $id, string $name, string $description, int $priceCents, int $isActive = 1): int
    {
        $pdo = Database::pdo();
        if ($id) {
            $stmt = $pdo->prepare('UPDATE packages SET name=:name, description=:description, price_cents=:price, is_active=:active WHERE id=:id');
            $stmt->execute([':name' => $name, ':description' => $description, ':price' => $priceCents, ':active' => $isActive, ':id' => $id]);
            return $id;
        }
        $stmt = $pdo->prepare('INSERT INTO packages (name, description, price_cents, is_active) VALUES (:name, :description, :price, :active)');
        $stmt->execute([':name' => $name, ':description' => $description, ':price' => $priceCents, ':active' => $isActive]);
        return (int) $pdo->lastInsertId();
    }

    public function delete(int $id): void
    {
        $pdo = Database::pdo();
        $stmt = $pdo->prepare('DELETE FROM packages WHERE id=:id');
        $stmt->execute([':id' => $id]);
    }
}

