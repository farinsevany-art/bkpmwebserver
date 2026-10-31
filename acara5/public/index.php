<?php
require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../routes/web.php';

require_once __DIR__ . '/../app/Controllers/HomeController.php';
require_once __DIR__ . '/../app/Controllers/MahasiswaController.php';
$uri = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?? '/';
$method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
$basePath = rtrim(BASE_PATH, '/');

if ($basePath !== '' && str_starts_with($uri, $basePath)) {
    $uri = substr($uri, strlen($basePath));
}

$uri = '/' . trim($uri, '/');

if ($uri === '/index.php' || $uri === '') {
    $uri = '/';
}

if (isset($routes[$method][$uri])) {

    [$controllerName, $action] = $routes[$method][$uri];

    $controller = new $controllerName();

    $controller->$action();

    exit;
}

if (
    $method === 'GET' &&
    preg_match('#^/mahasiswa/([0-9]+)$#', $uri, $matches)
) {

    $id = (int) $matches[1];

    $controller = new MahasiswaController();

    $controller->show($id);

    exit;
}
http_response_code(404);

echo '<!DOCTYPE html>';
echo '<html lang="id">';
echo '<head>';
echo '<meta charset="UTF-8">';
echo '<meta name="viewport" content="width=device-width, initial-scale=1.0">';
echo '<title>404 - Halaman Tidak Ditemukan</title>';
echo '</head>';

echo '<body>';

echo '<h2>404 - Halaman tidak ditemukan</h2>';

echo '<p>URL yang diminta tidak terdaftar pada routing aplikasi.</p>';

echo '</body>';
echo '</html>';

?>
```