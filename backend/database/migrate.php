<?php
declare(strict_types=1);

use App\Config\Env;

require __DIR__ . '/../vendor/autoload.php';

Env::bootstrap(__DIR__ . '/..');

$dsn = sprintf('mysql:host=%s;port=%s;dbname=%s;charset=utf8mb4',
    $_ENV['DB_HOST'] ?? 'localhost',
    $_ENV['DB_PORT'] ?? '3306',
    $_ENV['DB_NAME'] ?? ''
);

$pdo = new PDO($dsn, $_ENV['DB_USER'] ?? 'root', $_ENV['DB_PASS'] ?? '', [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
]);

$dir = __DIR__ . '/migrations';
$files = glob($dir . '/*.sql');
sort($files);

foreach ($files as $file) {
    echo "Running migration: " . basename($file) . PHP_EOL;
    $sql = file_get_contents($file);
    $pdo->exec($sql);
}

echo "Migrations completed." . PHP_EOL;

