<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Http\Request;
use App\Http\Response;
use App\Repositories\BookingRepository;
use App\Repositories\PackageRepository;
use App\Security\AuthMiddleware;

class BookingController
{
    public function __construct(
        private readonly BookingRepository $bookings = new BookingRepository(),
        private readonly PackageRepository $packages = new PackageRepository()
    ) {}

    public function create(Request $req, Response $res): void
    {
        $claims = AuthMiddleware::requireAuth($req, $res);
        $userId = (int) ($claims['sub'] ?? 0);
        $packageId = (int) ($req->body['package_id'] ?? 0);
        $date = (string) ($req->body['class_date'] ?? '');
        if ($userId <= 0 || $packageId <= 0 || !preg_match('/^\d{4}-\d{2}-\d{2}$/', $date)) {
            $res->json(['error' => 'Invalid input'], 400);
        }
        $id = $this->bookings->create($userId, $packageId, $date);
        $res->json(['booking_id' => $id, 'status' => 'pending']);
    }
}

