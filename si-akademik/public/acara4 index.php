<?php

require_once __DIR__ . '/../app/Models/Mahasiswa.php';

$mahasiswa = [
    new Mahasiswa(
        '240101001',
        'Farin Sevany',
        'Teknik Informatika'
    ),

    new Mahasiswa(
        '240101002',
        'Rendi Pranata',
        'Teknik Informatika'
    ),

    new Mahasiswa(
        '240101003',
        'Ratu Alyvia Meydiandra',
        'Teknik Informatika'
    )
];

require_once __DIR__ . '/../app/Views/mahasiswa/index.php';
