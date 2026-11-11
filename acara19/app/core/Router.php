<?php

class Router {
    private array $map = [
        'mahasiswa' => 'MahasiswaController',
        'register'  => 'AuthController',
    ];

    public function dispatch(): void {
        $url = $_GET['url'] ?? '';
        $url = rtrim($url, '/');
        $urlSegments = explode('/', $url);
        $resource = $urlSegments[0] ?? '';

        if (!array_key_exists($resource, $this->map)) {
            Response::json(404, false, 'Endpoint tidak ditemukan');
        }

        $controllerName = $this->map[$resource];
        $controller = new $controllerName();

        if ($resource === 'register') {
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                Response::json(405, false, 'HTTP Method tidak diizinkan untuk endpoint ini');
            }
            $controller->register();
            return;
        }

        $allowedMethods = ['GET', 'POST', 'PUT', 'DELETE'];
        if (!in_array($_SERVER['REQUEST_METHOD'], $allowedMethods)) {
            Response::json(405, false, 'HTTP Method tidak diizinkan untuk endpoint ini');
        }

        $controller->index();
    }
}
