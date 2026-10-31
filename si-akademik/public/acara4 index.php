<?php

require_once __DIR__ . '/../app/Models/Mahasiswa.php';

$mhs1 = new Mahasiswa(
    "240101001",
    "Farin Sevany",
    "Teknik Informatika"
);

$mhs2 = new Mahasiswa(
    "240101002",
    "Rendi Pranata",
    "Teknik Informatika"
);

$daftarMahasiswa = [$mhs1, $mhs2];

$content = __DIR__ . '/../app/Views/mahasiswa/index.php';

require __DIR__ . '/../app/Views/layouts/main.php';