<?php

declare(strict_types=1);

namespace App\Http;

final class Router
{
    /** @var array<int, array{method:string, pattern:string, handler:callable}> */
    private array $routes = [];

    public function get(string $path, callable $handler): void
    {
        $this->routes[] = ['method' => 'GET', 'pattern' => $path, 'handler' => $handler];
    }

    public function post(string $path, callable $handler): void
    {
        $this->routes[] = ['method' => 'POST', 'pattern' => $path, 'handler' => $handler];
    }

    public function dispatch(Request $request): Response
    {
        $method = $request->method();
        $path = rtrim($request->path(), '/') ?: '/';
        foreach ($this->routes as $route) {
            if ($route['method'] !== $method) {
                continue;
            }
            $parameters = [];
            if ($this->match($route['pattern'], $path, $parameters)) {
                $response = ($route['handler'])(...array_merge([$request], $parameters));
                if ($response instanceof Response) {
                    return $response;
                }

                return new Response((string) $response);
            }
        }

        return new Response($this->render404(), 404);
    }

    private function match(string $pattern, string $path, array &$parameters): bool
    {
        if ($pattern === $path) {
            $parameters = [];

            return true;
        }
        $regex = preg_replace('#\{([a-z_]+)\}#i', '(?P<$1>[^/]+)', $pattern);
        $regex = '#^' . rtrim($regex, '/') . '$#i';
        if (preg_match($regex, $path, $matches)) {
            foreach ($matches as $key => $value) {
                if (!is_int($key)) {
                    $parameters[] = ctype_digit($value) ? (int) $value : $value;
                }
            }

            return true;
        }

        return false;
    }

    private function render404(): string
    {
        return '<!doctype html><html lang="en"><head><meta charset="utf-8"><title>Not found</title></head><body><h1>404 Not Found</h1></body></html>';
    }
}
