<?php
declare(strict_types=1);

namespace App\Http;

class Router
{
    /** @var array<string, array<string, callable>> */
    private array $routes = [];

    public function get(string $path, callable $handler): void
    {
        $this->map('GET', $path, $handler);
    }

    public function post(string $path, callable $handler): void
    {
        $this->map('POST', $path, $handler);
    }

    public function put(string $path, callable $handler): void
    {
        $this->map('PUT', $path, $handler);
    }

    public function delete(string $path, callable $handler): void
    {
        $this->map('DELETE', $path, $handler);
    }

    public function map(string $method, string $path, callable $handler): void
    {
        $method = strtoupper($method);
        $this->routes[$method][$path] = $handler;
    }

    public function dispatch(Request $request): mixed
    {
        $method = $request->method;
        $path = rtrim($request->path, '/') ?: '/';
        $handler = $this->routes[$method][$path] ?? null;

        if ($handler === null) {
            return null;
        }

        $response = new Response();
        return $handler($request, $response);
    }
}

