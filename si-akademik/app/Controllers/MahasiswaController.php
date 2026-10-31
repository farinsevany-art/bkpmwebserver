<?php

class MahasiswaController
{
    public function index(): void
    {
        echo "<h1>Data Mahasiswa</h1>";
        echo "<p>Ini adalah halaman data mahasiswa.</p>";
    }

    public function create(): void
    {
        echo "<h1>Tambah Mahasiswa</h1>";
        echo "<p>Ini adalah halaman tambah mahasiswa.</p>";
    }

    public function show(int $id): void
    {
        echo "<h1>Detail Mahasiswa</h1>";
        echo "<p>ID Mahasiswa: " . $id . "</p>";
    }
}
