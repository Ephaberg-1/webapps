<?php
declare(strict_types=1);

namespace App\Http;

class Request
{
    public function __construct(
        public readonly string $method,
        public readonly string $path,
        public readonly array $query,
        public readonly array $headers,
        public readonly array $cookies,
        public readonly array $body,
        public readonly ?string $rawBody
    ) {
    }

    public static function fromGlobals(): self
    {
        $method = strtoupper($_SERVER['REQUEST_METHOD'] ?? 'GET');
        $uri = $_SERVER['REQUEST_URI'] ?? '/';
        $path = parse_url($uri, PHP_URL_PATH) ?: '/';
        $queryString = $_SERVER['QUERY_STRING'] ?? '';
        parse_str($queryString, $query);

        $headers = function_exists('getallheaders') ? (getallheaders() ?: []) : [];
        $cookies = $_COOKIE ?? [];
        $rawBody = file_get_contents('php://input') ?: null;
        $contentType = $headers['Content-Type'] ?? $headers['content-type'] ?? '';
        $body = [];

        if (stripos($contentType, 'application/json') !== false && $rawBody) {
            $decoded = json_decode($rawBody, true);
            if (is_array($decoded)) {
                $body = $decoded;
            }
        } elseif (stripos($contentType, 'application/x-www-form-urlencoded') !== false) {
            $body = $_POST ?? [];
        } else {
            $body = $_POST ?? [];
        }

        return new self($method, $path, $query, $headers, $cookies, $body, $rawBody);
    }
}

