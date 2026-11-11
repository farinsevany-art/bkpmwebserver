<?php
use App\Core\Router;

$router = new Router();

# Web MVC Routes (dari Acara 14)
$router->get('/', ['MahasiswaController', 'index']);
$router->get('/mahasiswa', ['MahasiswaController', 'index']);

# API Endpoints (Sesuai Prosedur Acara 15)
$router->get('/api/mahasiswa', ['ApiMahasiswaController', 'index']);
$router->post('/api/mahasiswa', ['ApiMahasiswaController', 'store']);
$router->options('/api/mahasiswa', ['ApiMahasiswaController', 'preflight']);

return $router;
