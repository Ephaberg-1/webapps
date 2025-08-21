<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Http\Request;
use App\Http\Response;
use App\Repositories\BookingRepository;
use App\Repositories\PaymentRepository;
use App\Repositories\UserRepository;
use App\Services\PaystackService;
use App\Security\AuthMiddleware;
use App\Config\Env;

class PaymentController
{
    public function __construct(
        private readonly BookingRepository $bookings = new BookingRepository(),
        private readonly PaymentRepository $payments = new PaymentRepository(),
        private readonly UserRepository $users = new UserRepository(),
        private readonly PaystackService $paystack = new PaystackService()
    ) {}

    public function init(Request $req, Response $res): void
    {
        $claims = AuthMiddleware::requireAuth($req, $res);
        $userId = (int) ($claims['sub'] ?? 0);
        $bookingId = (int) ($req->body['booking_id'] ?? 0);
        if ($bookingId <= 0) {
            $res->json(['error' => 'Invalid booking'], 400);
        }
        $booking = $this->bookings->findById($bookingId);
        if (!$booking || (int)$booking['user_id'] !== $userId) {
            $res->json(['error' => 'Not found'], 404);
        }
        // Lookup price from package
        $packageId = (int) $booking['package_id'];
        $pdo = \App\Database\Database::pdo();
        $stmt = $pdo->prepare('SELECT price_cents FROM packages WHERE id = :id');
        $stmt->execute([':id' => $packageId]);
        $price = (int) ($stmt->fetchColumn() ?: 0);

        $user = $this->users->findByEmail($req->body['email'] ?? '');
        if (!$user) {
            // fallback: get user email from id
            $userStmt = $pdo->prepare('SELECT email FROM users WHERE id = :id');
            $userStmt->execute([':id' => $userId]);
            $userEmail = (string) $userStmt->fetchColumn();
        } else {
            $userEmail = $user['email'];
        }

        $reference = 'bk_' . $bookingId . '_' . bin2hex(random_bytes(6));
        $this->payments->create($bookingId, $userId, $reference, $price);
        $callbackUrl = rtrim(Env::get('APP_URL', ''), '/') . '/api/payments/callback';
        $resp = $this->paystack->initialize($userEmail, $price, $reference, $callbackUrl);
        $res->json(['paystack' => $resp]);
    }

    public function callback(Request $req, Response $res): void
    {
        $reference = (string) ($req->query['reference'] ?? ($req->body['reference'] ?? ''));
        if ($reference === '') {
            $res->json(['error' => 'Missing reference'], 400);
        }
        $verify = $this->paystack->verify($reference);
        if (($verify['data']['status'] ?? '') === 'success') {
            $this->payments->markStatusByReference($reference, 'success', $verify);
            // Update booking status foreign keyed by payment
            $pdo = \App\Database\Database::pdo();
            $stmt = $pdo->prepare('SELECT booking_id, user_id FROM payments WHERE reference = :ref');
            $stmt->execute([':ref' => $reference]);
            $row = $stmt->fetch(\PDO::FETCH_ASSOC);
            if ($row) {
                (new \App\Repositories\BookingRepository())->updateStatus((int)$row['booking_id'], 'paid');
            }
            $res->json(['verified' => true]);
        } else {
            $this->payments->markStatusByReference($reference, 'failed', $verify);
            $res->json(['verified' => false], 400);
        }
    }
}

