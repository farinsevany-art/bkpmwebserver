<?php
$auth = AuthMiddleware::class;

// Auth
$router->get('/', [AuthController::class, 'loginForm']);
$router->get('/login', [AuthController::class, 'loginForm']);
$router->post('/login', [AuthController::class, 'login']);
$router->post('/logout', [AuthController::class, 'logout']);

$router->get('/dashboard', [HomeController::class, 'dashboard'], [$auth]);

// CRUD Mahasiswa (+ search, pagination, detail)
$router->get('/mahasiswa', [MahasiswaController::class, 'index'], [$auth]);
$router->get('/mahasiswa/create', [MahasiswaController::class, 'create'], [$auth]);
$router->post('/mahasiswa/store', [MahasiswaController::class, 'store'], [$auth]);
$router->get('/mahasiswa/{id}', [MahasiswaController::class, 'show'], [$auth]);
$router->get('/mahasiswa/{id}/edit', [MahasiswaController::class, 'edit'], [$auth]);
$router->post('/mahasiswa/{id}/update', [MahasiswaController::class, 'update'], [$auth]);
$router->post('/mahasiswa/{id}/delete', [MahasiswaController::class, 'destroy'], [$auth]);

// CRUD Prodi
$router->get('/prodi', [ProdiController::class, 'index'], [$auth]);
$router->get('/prodi/create', [ProdiController::class, 'create'], [$auth]);
$router->post('/prodi/store', [ProdiController::class, 'store'], [$auth]);
$router->get('/prodi/{id}/edit', [ProdiController::class, 'edit'], [$auth]);
$router->post('/prodi/{id}/update', [ProdiController::class, 'update'], [$auth]);
$router->post('/prodi/{id}/delete', [ProdiController::class, 'destroy'], [$auth]);

// CRUD Mata Kuliah
$router->get('/mata-kuliah', [MataKuliahController::class, 'index'], [$auth]);
$router->get('/mata-kuliah/create', [MataKuliahController::class, 'create'], [$auth]);
$router->post('/mata-kuliah/store', [MataKuliahController::class, 'store'], [$auth]);
$router->get('/mata-kuliah/{id}/edit', [MataKuliahController::class, 'edit'], [$auth]);
$router->post('/mata-kuliah/{id}/update', [MataKuliahController::class, 'update'], [$auth]);
$router->post('/mata-kuliah/{id}/delete', [MataKuliahController::class, 'destroy'], [$auth]);
