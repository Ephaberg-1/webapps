<?php
declare(strict_types=1);

use App\Config\Env;
use App\Http\Request;
use App\Http\Response;
use App\Http\Router;
use App\Security\SecurityHeaders;
use App\Controllers\AuthController;
use App\Controllers\PackageController;
use App\Controllers\BookingController;
use App\Controllers\PaymentController;
use App\Controllers\TwoFactorController;
use App\Controllers\AdminController;
use App\Controllers\UtilityController;

require __DIR__ . '/../vendor/autoload.php';

// Load environment variables
Env::bootstrap(__DIR__ . '/..');

// Initialize security headers and CORS
$response = new Response();
$securityHeaders = new SecurityHeaders(
    frontendOrigin: Env::get('FRONTEND_ORIGIN', '*'),
    appEnv: Env::get('APP_ENV', 'production')
);
$securityHeaders->applyCommon();
$securityHeaders->applyCors();

// Handle CORS preflight early
if (strtoupper($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'OPTIONS') {
    http_response_code(204);
    exit; // No body for preflight
}

// Build request
$request = Request::fromGlobals();

// Router with basic health endpoint
$router = new Router();
$router->get('/health', function (Request $req, Response $res) {
    return $res->json([
        'status' => 'ok',
        'time' => gmdate('c'),
    ]);
});

// Auth endpoints
$auth = new AuthController();
$router->post('/api/auth/register', [$auth, 'register']);
$router->post('/api/auth/login', [$auth, 'login']);
$router->post('/api/auth/refresh', [$auth, 'refresh']);
$router->post('/api/auth/logout', [$auth, 'logout']);

// Package endpoints
$pkg = new PackageController();
$router->get('/api/packages', [$pkg, 'list']);
$router->post('/api/admin/packages/upsert', [$pkg, 'upsert']);
$router->post('/api/admin/packages/delete', [$pkg, 'delete']);

// Booking endpoints
$book = new BookingController();
$router->post('/api/bookings', [$book, 'create']);

// Payment endpoints
$pay = new PaymentController();
$router->post('/api/payments/init', [$pay, 'init']);
$router->post('/api/payments/callback', [$pay, 'callback']);

// 2FA endpoints
$twofa = new TwoFactorController();
$router->post('/api/2fa/setup', [$twofa, 'setup']);
$router->post('/api/2fa/enable', [$twofa, 'enable']);
$router->post('/api/2fa/verify', [$twofa, 'verify']);

// Admin endpoints
$admin = new AdminController();
$router->post('/api/admin/broadcast', [$admin, 'broadcast']);

// Utility endpoints
$util = new UtilityController();
$router->get('/api/csrf', [$util, 'csrf']);
$router->post('/api/ai', [$util, 'ai']);

// Dispatch
$routeResult = $router->dispatch($request);

if ($routeResult === null) {
    $response->json(['error' => 'Not Found'], 404);
}

