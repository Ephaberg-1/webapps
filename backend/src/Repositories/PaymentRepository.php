<?php
declare(strict_types=1);

namespace App\Repositories;

use App\Database\Database;
use PDO;

class PaymentRepository
{
    public function create(int $bookingId, int $userId, string $reference, int $amountCents, string $currency = 'NGN'): int
    {
        $pdo = Database::pdo();
        $stmt = $pdo->prepare('INSERT INTO payments (booking_id, user_id, reference, amount_cents, currency) VALUES (:booking_id, :user_id, :reference, :amount, :currency)');
        $stmt->execute([
            ':booking_id' => $bookingId,
            ':user_id' => $userId,
            ':reference' => $reference,
            ':amount' => $amountCents,
            ':currency' => $currency,
        ]);
        return (int) $pdo->lastInsertId();
    }

    public function markStatusByReference(string $reference, string $status, ?array $payload = null): void
    {
        $pdo = Database::pdo();
        $stmt = $pdo->prepare('UPDATE payments SET status = :status, raw_payload = :payload WHERE reference = :reference');
        $stmt->execute([
            ':status' => $status,
            ':payload' => $payload ? json_encode($payload, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) : null,
            ':reference' => $reference,
        ]);
    }
}

