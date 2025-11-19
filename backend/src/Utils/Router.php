<?php

namespace App\Utils;

class Router
{
    private array $routes = [];

    public function add(string $method, string $pattern, callable $handler, array $middleware = []): void
    {
        $this->routes[] = [
            'method' => strtoupper($method),
            'pattern' => '#^' . rtrim($pattern, '/') . '$#',
            'handler' => $handler,
            'middleware' => $middleware,
        ];
    }

    public function dispatch(string $method, string $uri)
    {
        $uri = parse_url($uri, PHP_URL_PATH);
        $uri = '/' . trim($uri, '/');
        if ($uri === '//') {
            $uri = '/';
        }

        foreach ($this->routes as $route) {
            if ($route['method'] !== strtoupper($method)) {
                continue;
            }

            if (preg_match($route['pattern'], $uri, $matches)) {
                array_shift($matches);
                $request = [
                    'params' => $matches,
                ];

                foreach ($route['middleware'] as $middleware) {
                    $result = $middleware($request);
                    if ($result === false) {
                        return null;
                    }
                    $request = $result;
                }

                return call_user_func($route['handler'], $request);
            }
        }

        http_response_code(404);
        echo json_encode(['error' => 'Not Found']);
    }
}
