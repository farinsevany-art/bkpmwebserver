<?php

class Router {
    private array $map = [
        'mahasiswa' => 'MahasiswaController',
        'register'  => 'AuthController',
        'login'     => 'AuthController',
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

        // Penanganan endpoint login
        if ($resource === 'login') {
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                Response::json(405, false, 'HTTP Method tidak diizinkan untuk endpoint ini');
            }
            $controller->login();
            return;
        }

        // Penanganan endpoint register
        if ($resource === 'register') {
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                Response::json(405, false, 'HTTP Method tidak diizinkan untuk endpoint ini');
            }
            $controller->register();
            return;
        }

        // Penanganan endpoint CRUD (Mahasiswa)
        $method = $_SERVER['REQUEST_METHOD'];
        $id = $urlSegments[1] ?? null;

        switch ($method) {
            case 'GET':
                $controller->index();
                break;
            case 'POST':
                $controller->store();
                break;
            case 'PUT':
                if (!$id) Response::json(400, false, 'ID required for UPDATE');
                $controller->update((int)$id);
                break;
            case 'DELETE':
                if (!$id) Response::json(400, false, 'ID required for DELETE');
                $controller->destroy((int)$id);
                break;
            default:
                Response::json(405, false, 'HTTP Method tidak diizinkan');
                break;
        }
    }
}
