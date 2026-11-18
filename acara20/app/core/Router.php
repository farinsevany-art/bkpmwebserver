<?php

class Router {
    private array $map = [
        'mahasiswa' => 'MahasiswaController',
        'register' => 'AuthController',
        'login' => 'AuthController',
    ];

    public function dispatch(): void {
        $url = trim($_GET['url'] ?? 'mahasiswa', '/');
        $segments = $url === '' ? [] : explode('/', $url);
        $resource = $segments[0] ?? 'mahasiswa';

        if (!array_key_exists($resource, $this->map)) {
            Response::json(404, false, 'Endpoint tidak ditemukan');
        }

        $controllerName = $this->map[$resource];
        $controller = new $controllerName();

        if (in_array($resource, ['login', 'register'], true)) {
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                Response::json(405, false, 'HTTP Method tidak diizinkan untuk endpoint ini');
            }

            $action = $resource;
            $controller->$action();
            return;
        }

        $nim = $segments[1] ?? ($_GET['nim'] ?? ($_GET['id'] ?? null));
        switch ($_SERVER['REQUEST_METHOD']) {
            case 'GET':
                $controller->index($nim);
                break;
            case 'POST':
                $controller->store();
                break;
            case 'PUT':
                $controller->update($nim);
                break;
            case 'DELETE':
                $controller->destroy($nim);
                break;
            default:
                Response::json(405, false, 'HTTP Method tidak diizinkan untuk endpoint ini');
        }
    }
}
