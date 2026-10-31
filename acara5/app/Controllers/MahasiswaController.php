<?php

class MahasiswaController
{
    public function index(): void
    {
        echo '<h2>Daftar Mahasiswa</h2>';
        echo '<p>Ini adalah halaman daftar mahasiswa.</p>';
        echo '<p><a href="' . BASE_PATH . '/mahasiswa/5">Lihat detail mahasiswa ID 5</a></p>';
        echo '<p><a href="' . BASE_PATH . '/mahasiswa/create">Tambah mahasiswa</a></p>';
    }

    public function create(): void
    {
        echo '<h2>Form Tambah Mahasiswa</h2>';
        echo '<p>Contoh halaman untuk menambahkan mahasiswa.</p>';
        echo '<p><a href="' . BASE_PATH . '/mahasiswa">Kembali ke daftar mahasiswa</a></p>';
    }

    public function show(int $id): void
    {
        echo '<h2>Detail mahasiswa dengan ID = ' . htmlspecialchars((string) $id, ENT_QUOTES, 'UTF-8') . '</h2>';
    }
}
