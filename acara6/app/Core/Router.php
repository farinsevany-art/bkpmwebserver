<?php
class Router
{
    private array $routes = [];

    public function get(string $path, array $action, array $middleware = []): void
    {
        $this->add('GET', $path, $action, $middleware);
    }

    public function post(string $path, array $action, array $middleware = []): void
    {
        $this->add('POST', $path, $action, $middleware);
    }

    private function add(string $method, string $path, array $action, array $middleware): void
    {
        $this->routes[$method][$this->normalize($path)] = [
            'action' => $action,
            'middleware' => $middleware,
        ];
    }

    public function dispatch(string $method, string $path): void
    {
        $path = $this->normalize($path);
        $route = $this->routes[$method][$path] ?? null;

        if (!$route) {
            http_response_code(404);
            echo '<h1>404 - Halaman tidak ditemukan</h1><p><a href="login">Kembali ke login</a></p>';
            return;
        }

        foreach ($route['middleware'] as $middlewareClass) {
            (new $middlewareClass())->handle();
        }

        [$controllerClass, $methodName] = $route['action'];
        (new $controllerClass())->$methodName();
    }

    private function normalize(string $path): string
    {
        $path = '/' . trim($path, '/');
        return $path === '/' ? '/' : rtrim($path, '/');
    }
}
