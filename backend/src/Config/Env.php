<?php
declare(strict_types=1);

namespace App\Config;

use Dotenv\Dotenv;

class Env
{
    public static function bootstrap(string $basePath): void
    {
        if (file_exists($basePath . '/.env')) {
            $dotenv = Dotenv::createImmutable($basePath);
            $dotenv->safeLoad();
        }

        // Defaults
        $_ENV['APP_ENV'] = $_ENV['APP_ENV'] ?? 'production';
        $_ENV['APP_DEBUG'] = $_ENV['APP_DEBUG'] ?? '0';
    }

    public static function get(string $key, ?string $default = null): ?string
    {
        return $_ENV[$key] ?? getenv($key) ?: $default;
    }
}

