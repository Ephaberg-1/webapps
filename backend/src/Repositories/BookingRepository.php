<?php
declare(strict_types=1);

namespace App\Repositories;

use App\Database\Database;
use PDO;

class BookingRepository
{
    public function create(int $userId, int $packageId, string $classDate): int
    {
        $pdo = Database::pdo();
        $stmt = $pdo->prepare('INSERT INTO bookings (uuid, user_id, package_id, class_date) VALUES (UUID(), :user_id, :package_id, :class_date)');
        $stmt->execute([':user_id' => $userId, ':package_id' => $packageId, ':class_date' => $classDate]);
        return (int) $pdo->lastInsertId();
    }

    public function findById(int $id): ?array
    {
        $pdo = Database::pdo();
        $stmt = $pdo->prepare('SELECT * FROM bookings WHERE id = :id');
        $stmt->execute([':id' => $id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ?: null;
    }

    public function updateStatus(int $id, string $status): void
    {
        $pdo = Database::pdo();
        $stmt = $pdo->prepare('UPDATE bookings SET status = :status WHERE id = :id');
        $stmt->execute([':status' => $status, ':id' => $id]);
    }
}

