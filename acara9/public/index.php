<?php
declare(strict_types=1);

session_start();

define('BASE_PATH', dirname(__DIR__));

// Autoload sederhana: nama class = nama file
spl_autoload_register(function (string $class): void {
    $dirs = ['/app/Core/', '/app/Core/Middleware/', '/app/Controllers/', '/app/Models/'];
    foreach ($dirs as $dir) {
        $file = BASE_PATH . $dir . $class . '.php';
        if (is_file($file)) {
            require_once $file;
            return;
        }
    }
});

$router = new Router();
require BASE_PATH . '/routes/web.php';

$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH) ?? '/';
$baseUrl = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME']));
if ($baseUrl !== '/' && $baseUrl !== '.') {
    $uri = substr($uri, strlen($baseUrl)) ?: '/';
}
$method = $_SERVER['REQUEST_METHOD'] ?? 'GET';

$router->dispatch($method, $uri);
