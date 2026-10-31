<?php

class HomeController
{
    public function index(): void
    {
        echo '<h2>Halaman Home</h2>';
        echo '<p>Selamat datang di aplikasi Si-Akademik.</p>';
        echo '<p><a href="' . BASE_PATH . '/mahasiswa">Lihat daftar mahasiswa</a></p>';
    }
}
