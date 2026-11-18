<?php

class Router {
    private array $map = [
        'mahasiswa' => 'MahasiswaController',
    ];

    public function dispatch(): void {
        $url = $_GET['url'] ?? '';
        $url = rtrim($url, '/');
        $urlSegments = array_values(array_filter(explode('/', $url), 'strlen'));
        $resource = $urlSegments[0] ?? '';

        if (!array_key_exists($resource, $this->map)) {
            Response::json(404, false, 'Endpoint tidak ditemukan');
        }

        $method = $_SERVER['REQUEST_METHOD'];
        if (!in_array($method, ['GET', 'POST', 'PUT', 'DELETE'], true)) {
            Response::json(405, false, 'HTTP Method tidak diizinkan untuk endpoint ini');
        }

        $controllerName = $this->map[$resource];
        $controller = new $controllerName();
        $nim = $urlSegments[1] ?? ($_GET['nim'] ?? ($_GET['id'] ?? null));

        switch ($method) {
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
