<?php
class HomeController extends Controller
{
    public function dashboard(): void
    {
        $flash = $_SESSION['flash'] ?? null;
        unset($_SESSION['flash']);

        $mahasiswa = $_SESSION['mahasiswa'] ?? [
            ['nim' => 'E41240001', 'nama' => 'Andi Pratama', 'prodi' => 'Manajemen Informatika'],
            ['nim' => 'E41240002', 'nama' => 'Siti Aminah', 'prodi' => 'Teknik Informatika'],
        ];

        $this->view('dashboard/index', [
            'flash' => $flash,
            'jumlahMahasiswa' => count($mahasiswa),
            'namaAdmin' => $_SESSION['user_name'] ?? 'Admin',
        ]);
    }
}
