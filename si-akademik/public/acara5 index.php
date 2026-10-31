
<?php

require_once __DIR__ . '/../routes/web.php';

$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

$base = '/minngu1/si-akademik/public';

if (str_starts_with($uri, $base)) {
    $uri = substr($uri, strlen($base));
}

$uri = $uri === '' ? '/' : $uri;
$uri = rtrim($uri, '/') ?: '/';

$method = $_SERVER['REQUEST_METHOD'];

if (isset($routes[$method][$uri])) {
    [$controllerName, $action] = $routes[$method][$uri];

    $controllers = [
        'HomeController' => __DIR__ . '/../app/Controllers/HomeController.php',
        'MahasiswaController' => __DIR__ . '/../app/Controllers/MahasiswaController.php',
    ];

    if (!isset($controllers[$controllerName])) {
        http_response_code(404);
        exit('Controller tidak ditemukan.');
    }

    require_once $controllers[$controllerName];

    $controller = new $controllerName();

    if (!is_callable([$controller, $action])) {
        http_response_code(404);
        exit('Method tidak ditemukan.');
    }

    $controller->$action();
} else {
    http_response_code(404);
    echo '404 - Halaman tidak ditemukan';
}
