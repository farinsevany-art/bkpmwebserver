<?php
require_once __DIR__ . '/../app/Models/Mahasiswa.php';

// Membuat beberapa objek Mahasiswa
$mhs1 = new Mahasiswa("240101001", "Farin Sevany", "Teknik Informatika");
$mhs2 = new Mahasiswa("240101002", "Rendi Pranata", "Teknik Informatika");

$daftarMahasiswa = [$mhs1, $mhs2];

// Mengirim data ke View
$content = __DIR__ . '/../app/Views/mahasiswa/index.php';
require __DIR__ . '/../app/Views/layouts/main.php';
