<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Http\Request;
use App\Http\Response;
use App\Repositories\PackageRepository;
use App\Security\AuthMiddleware;

class PackageController
{
    public function __construct(private readonly PackageRepository $repo = new PackageRepository()) {}

    public function list(Request $req, Response $res): void
    {
        $res->json(['packages' => $this->repo->listActive()]);
    }

    public function upsert(Request $req, Response $res): void
    {
        $claims = AuthMiddleware::requireAuth($req, $res);
        AuthMiddleware::requireAdmin($claims, $res);

        $id = isset($req->body['id']) ? (int) $req->body['id'] : null;
        $name = trim((string) ($req->body['name'] ?? ''));
        $description = trim((string) ($req->body['description'] ?? ''));
        $price = (int) ($req->body['price_cents'] ?? 0);
        $active = (int) ($req->body['is_active'] ?? 1);

        if ($name === '' || $description === '' || $price <= 0) {
            $res->json(['error' => 'Invalid input'], 400);
        }
        $newId = $this->repo->upsert($id, $name, $description, $price, $active);
        $res->json(['id' => $newId]);
    }

    public function delete(Request $req, Response $res): void
    {
        $claims = AuthMiddleware::requireAuth($req, $res);
        AuthMiddleware::requireAdmin($claims, $res);
        $id = (int) ($req->body['id'] ?? 0);
        if ($id <= 0) {
            $res->json(['error' => 'Invalid id'], 400);
        }
        $this->repo->delete($id);
        $res->json(['deleted' => true]);
    }
}

