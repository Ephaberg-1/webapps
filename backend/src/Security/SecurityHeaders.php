<?php
declare(strict_types=1);

namespace App\Security;

use App\Config\Env;

class SecurityHeaders
{
    public function __construct(
        private readonly string $frontendOrigin,
        private readonly string $appEnv = 'production'
    ) {
    }

    public function applyCommon(): void
    {
        // HSTS (only on HTTPS environments)
        if (!empty($_SERVER['HTTPS']) || (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https')) {
            header('Strict-Transport-Security: max-age=31536000; includeSubDomains; preload');
        }

        header('X-Frame-Options: DENY');
        header('X-Content-Type-Options: nosniff');
        header('X-XSS-Protection: 0');
        header('Referrer-Policy: no-referrer');
        header('Permissions-Policy: geolocation=(), microphone=(), camera=()');

        // Basic CSP; frontend will use nonces/hashes if needed. Adjust as app grows.
        $frontend = $this->frontendOrigin ?: "'none'";
        $csp = [
            "default-src 'none'",
            "base-uri 'self'",
            "script-src 'self' 'unsafe-inline' $frontend https://www.google.com https://www.gstatic.com https://www.recaptcha.net",
            "style-src 'self' 'unsafe-inline' $frontend",
            "img-src 'self' data: $frontend",
            "font-src 'self' data:",
            "connect-src 'self' $frontend https://api.paystack.co",
            "frame-src https://js.paystack.co https://www.google.com https://www.recaptcha.net",
            "form-action 'self' $frontend",
            "frame-ancestors 'none'",
            "upgrade-insecure-requests"
        ];
        header('Content-Security-Policy: ' . implode('; ', $csp));

        // Remove x-powered-by if set
        header_remove('X-Powered-By');
    }

    public function applyCors(): void
    {
        $origin = $_SERVER['HTTP_ORIGIN'] ?? '';
        $allowedOrigin = $this->frontendOrigin;

        if ($origin && $allowedOrigin && hash_equals($allowedOrigin, $origin)) {
            header('Access-Control-Allow-Origin: ' . $allowedOrigin);
            header('Vary: Origin');
        }

        header('Access-Control-Allow-Credentials: true');
        header('Access-Control-Allow-Methods: GET, POST, PUT, PATCH, DELETE, OPTIONS');
        header('Access-Control-Allow-Headers: Content-Type, Authorization, X-CSRF-Token, X-Requested-With');
        header('Access-Control-Max-Age: 600');
    }
}

