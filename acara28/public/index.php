<?php

$appEnvironment = getenv('APP_ENV') ?: 'development';
$allowedOrigin = getenv('CORS_ALLOWED_ORIGIN');
if ($appEnvironment !== 'production') {
    header('Access-Control-Allow-Origin: *');
} elseif ($allowedOrigin !== false && $allowedOrigin !== '' && ($_SERVER['HTTP_ORIGIN'] ?? '') === $allowedOrigin) {
    header('Access-Control-Allow-Origin: ' . $allowedOrigin);
    header('Vary: Origin');
}
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(204);
    exit;
}

require_once __DIR__ . '/../app/core/autoload.php';

$router = new Router();
$router->dispatch();
