<?php
$auth = AuthMiddleware::class;

$router->get('/', [AuthController::class, 'loginForm']);
$router->get('/login', [AuthController::class, 'loginForm']);
$router->post('/login', [AuthController::class, 'login']);
$router->post('/logout', [AuthController::class, 'logout']);

$router->get('/dashboard', [HomeController::class, 'dashboard'], [$auth]);
$router->get('/mahasiswa', [MahasiswaController::class, 'index'], [$auth]);
$router->get('/mahasiswa/tambah', [MahasiswaController::class, 'create'], [$auth]);
$router->post('/mahasiswa/tambah', [MahasiswaController::class, 'store'], [$auth]);
