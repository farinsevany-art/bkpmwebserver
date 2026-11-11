<?php
namespace App\Core;

class Router {
    private $routes = [];

    public function get($path, $handler) {
        $this->routes['GET'][$path] = $handler;
    }

    public function post($path, $handler) {
        $this->routes['POST'][$path] = $handler;
    }

    public function options($path, $handler) {
        $this->routes['OPTIONS'][$path] = $handler;
    }

    public function dispatch($method, $uri) {
        $parsedUrl = parse_url($uri, PHP_URL_PATH);
        
        // Deteksi script name lokasi penempatan project di htdocs
        $scriptName = dirname($_SERVER['SCRIPT_NAME']); // misal: /minngu1/acara15/public
        
        // Bersihkan base path folder dari URI
        $path = str_replace($scriptName, '', $parsedUrl);
        
        // Fallback jika diakses tanpa mengakhiri /public
        $scriptParent = str_replace('/public', '', $scriptName);
        if ($scriptParent !== '' && strpos($path, $scriptParent) === 0) {
            $path = str_replace($scriptParent, '', $path);
        }

        // Hapus awalan /public jika masih tersisa
        if (strpos($path, '/public') === 0) {
            $path = substr($path, 7);
        }

        // Normalisasi path agar selalu diawali '/'
        $path = '/' . trim($path, '/');
        if (empty($path) || $path === '//') {
            $path = '/';
        }

        // Jalankan Controller & Action jika rute cocok
        if (isset($this->routes[$method][$path])) {
            $handler = $this->routes[$method][$path];
            $controllerName = "App\\Controllers\\" . $handler[0];
            $action = $handler[1];
            
            if (class_exists($controllerName)) {
                $controller = new $controllerName();
                if (method_exists($controller, $action)) {
                    $controller->$action();
                    return;
                }
            }
        }

        foreach ($this->routes as $routes) {
            if (isset($routes[$path])) {
                header('Allow: ' . implode(', ', array_keys(array_filter(
                    $this->routes,
                    function ($methodRoutes) use ($path) {
                        return isset($methodRoutes[$path]);
                    }
                ))));
                if (strpos($path, '/api') === 0) {
                    header('Content-Type: application/json; charset=UTF-8');
                    http_response_code(405);
                    echo json_encode(['success' => false, 'message' => 'Method tidak diizinkan']);
                } else {
                    http_response_code(405);
                    echo '405 Method Not Allowed';
                }
                return;
            }
        }

        // Penanganan jika Route tidak ditemukan (404)
        if (strpos($path, '/api') !== false) {
            header('Content-Type: application/json');
            http_response_code(404);
            echo json_encode(['success' => false, 'message' => 'Endpoint API tidak ditemukan']);
        } else {
            http_response_code(404);
            echo "404 Page Not Found";
        }
    }
}