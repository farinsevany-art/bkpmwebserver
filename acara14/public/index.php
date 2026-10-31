<?php
declare(strict_types=1);

session_start();
define('BASE_PATH', dirname(__DIR__));

spl_autoload_register(function (string $class): void {
    $dirs = ['/app/Core/', '/app/Core/Middleware/', '/app/Controllers/', '/app/Models/', '/app/Services/'];
    foreach ($dirs as $dir) {
        $file = BASE_PATH . $dir . $class . '.php';
        if (is_file($file)) { require_once $file; return; }
    }
});

// Perlindungan CSRF untuk seluruh request POST.
if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST') {
    $submitted = (string) ($_POST['_csrf_token'] ?? '');
    $stored = (string) ($_SESSION['_csrf_token'] ?? '');
    if ($submitted === '' || $stored === '' || !hash_equals($stored, $submitted)) {
        http_response_code(419);
        echo '<!doctype html><html lang="id"><meta charset="utf-8"><title>Permintaan tidak valid</title>';
        echo '<body style="font-family:Arial,sans-serif;padding:2rem"><h1>Permintaan tidak dapat diproses</h1>';
        echo '<p>Sesi formulir tidak valid atau kedaluwarsa. Silakan kembali dan coba lagi.</p></body></html>';
        exit;
    }
}

set_exception_handler(function (Throwable $e): void {
    AppLogger::error($e, 'Unhandled exception');
    if (!headers_sent()) { http_response_code(500); }
    echo '<!doctype html><html lang="id"><meta charset="utf-8"><title>Kesalahan Sistem</title>';
    echo '<body style="font-family:Arial,sans-serif;padding:2rem"><h1>Terjadi kesalahan sistem</h1>';
    echo '<p>Permintaan belum dapat diproses. Silakan coba lagi nanti.</p>';
    echo '<a href="' . htmlspecialchars('index.php', ENT_QUOTES, 'UTF-8') . '">Kembali</a></body></html>';
});

$router = new Router();
require BASE_PATH . '/routes/web.php';
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH) ?? '/';
$baseUrl = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME']));
if ($baseUrl !== '/' && $baseUrl !== '.') { $uri = substr($uri, strlen($baseUrl)) ?: '/'; }
$router->dispatch($_SERVER['REQUEST_METHOD'] ?? 'GET', $uri);
