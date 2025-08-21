<?php
declare(strict_types=1);

namespace App\Http;

class Response
{
    public function json(array $data, int $status = 200, array $headers = []): void
    {
        http_response_code($status);
        header('Content-Type: application/json; charset=utf-8');
        foreach ($headers as $name => $value) {
            header($name . ': ' . $value, true);
        }
        echo json_encode($data, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
        exit;
    }
}

