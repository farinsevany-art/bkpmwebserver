<?php

require_once __DIR__ . '/../routes/web.php';

$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

// Jika project berada di subfolder /si-akademik/public
$base = '/si-akademik/public';

if (str_starts_with($uri, $base)) {
    $uri = substr($uri, strlen($base)) ?: '/';
}

$method = $_SERVER['REQUEST_METHOD'];

if (isset($routes[$method][$uri])) {

    [$controllerName, $action] = $routes[$method][$uri];

    if ($controllerName === 'HomeController') {
        require_once __DIR__ . '/../App/Controllers/HomeController.php';
    }

    if ($controllerName === 'MahasiswaController') {
        require_once __DIR__ . '/../App/Controllers/MahasiswaController.php';
    }

    $controller = new $controllerName();

    $controller->$action();

} else {

    http_response_code(404);

    echo "404 - Halaman tidak ditemukan";
}