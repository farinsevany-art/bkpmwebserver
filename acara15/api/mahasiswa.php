<?php
require_once __DIR__ . '/../app/Core/Database.php';
require_once __DIR__ . '/../app/Models/Mahasiswa.php';
require_once __DIR__ . '/../app/Controllers/ApiMahasiswaController.php';

$controller = new \App\Controllers\ApiMahasiswaController();
$controller->handleRequest();
