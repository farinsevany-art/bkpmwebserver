<?php
class Router {
    private array $map = [
        'mahasiswa' => 'MahasiswaController',
        'buku'      => 'BukuController',
    ];

    public function dispatch(): void {
        $uri = trim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/');
        $segments = array_values(array_filter(explode('/', $uri), 'strlen'));
        $resourceIndex = null;

        foreach ($segments as $index => $segment) {
            if (isset($this->map[$segment])) {
                $resourceIndex = $index;
                break;
            }
        }

        $resource = $resourceIndex === null ? null : $segments[$resourceIndex];
        
        $controllerName = $resource !== null ? $this->map[$resource] : null;
        if (!$controllerName) {
            http_response_code(404);
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Endpoint API tidak ditemukan']);
            exit;
        }

        $controller = new $controllerName();
        $nim = $_GET['nim'] ?? $_GET['id'] ?? ($segments[$resourceIndex + 1] ?? null);

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
                http_response_code(405);
                header('Content-Type: application/json');
                echo json_encode(['success' => false, 'message' => 'Method tidak diizinkan']);
        }
    }
}
?>