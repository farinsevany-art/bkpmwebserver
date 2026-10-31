
<?php

class Router
{
    private array $routes = [];

    /**
     * Mendaftarkan route GET.
     */
    public function get(
        string $path,
        array $action,
        array $middleware = []
    ): void {
        $this->add('GET', $path, $action, $middleware);
    }

    /**
     * Mendaftarkan route POST.
     */
    public function post(
        string $path,
        array $action,
        array $middleware = []
    ): void {
        $this->add('POST', $path, $action, $middleware);
    }

    /**
     * Menambahkan route beserta pola URL.
     * Parameter {nim} menerima huruf, angka, underscore,
     * dan tanda hubung.
     */
    private function add(
        string $method,
        string $path,
        array $action,
        array $middleware
    ): void {
        $path = $this->normalize($path);

        $segments = explode('/', trim($path, '/'));
        $regexSegments = [];

        foreach ($segments as $segment) {
            if (
                preg_match(
                    '/^\{([a-z_][a-z0-9_]*)\}$/i',
                    $segment,
                    $matches
                )
            ) {
                $parameterName = strtolower($matches[1]);

                if ($parameterName === 'nim') {
                    $regexSegments[] = '([A-Za-z0-9_-]+)';
                } else {
                    $regexSegments[] = '([^/]+)';
                }
            } else {
                $regexSegments[] = preg_quote($segment, '#');
            }
        }

        $regex = '#^/' . implode('/', $regexSegments) . '$#';

        if ($path === '/') {
            $regex = '#^/$#';
        }

        $this->routes[strtoupper($method)][] = [
            'regex' => $regex,
            'action' => $action,
            'middleware' => $middleware,
        ];
    }

    /**
     * Mencocokkan URL dan menjalankan controller.
     */
    public function dispatch(string $method, string $path): void
    {
        $method = strtoupper($method);
        $path = $this->normalize($path);

        foreach ($this->routes[$method] ?? [] as $route) {
            if (!preg_match($route['regex'], $path, $matches)) {
                continue;
            }

            array_shift($matches);

            foreach ($route['middleware'] as $middlewareClass) {
                (new $middlewareClass())->handle();
            }

            [$controllerClass, $methodName] = $route['action'];

            if (
                !class_exists($controllerClass) ||
                !method_exists($controllerClass, $methodName)
            ) {
                http_response_code(500);
                echo '<h1>500 - Controller atau method tidak ditemukan</h1>';
                return;
            }

            // Mengubah parameter URL menjadi nilai asli.
            $matches = array_map(
                static fn(string $value): string => rawurldecode($value),
                $matches
            );

            $controller = class_exists(ControllerFactory::class)
                ? ControllerFactory::make($controllerClass)
                : new $controllerClass();

            $controller->$methodName(...$matches);
            return;
        }

        http_response_code(404);
        echo '<h1>404 - Halaman tidak ditemukan</h1>';
    }

    /**
     * Menyamakan format path URL.
     */
    private function normalize(string $path): string
    {
        $path = '/' . trim($path, '/');

        return $path === '/' ? '/' : rtrim($path, '/');
    }
}
