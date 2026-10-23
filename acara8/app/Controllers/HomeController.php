
<?php

class HomeController extends Controller
{
    
    public function dashboard(): void
    {
        // Pastikan hanya administrator yang sudah login
        // dapat membuka dashboard.
        if (empty($_SESSION['logged_in'])) {
            $this->redirect('login');
        }

        try {
            $mahasiswa = new Mahasiswa();
            $prodi = new Prodi();
            $mataKuliah = new MataKuliah();

            $this->view('dashboard/index', [
                'title' => 'Dashboard',
                'current' => 'dashboard',
                'namaAdmin' => $_SESSION['user_name'] ?? 'Admin',
                'jumlahMahasiswa' => $mahasiswa->total(),
                'jumlahProdi' => $prodi->total(),
                'jumlahMk' => $mataKuliah->total(),
            ]);
        } catch (Throwable $e) {
            error_log('Dashboard gagal dimuat: ' . $e->getMessage());

            http_response_code(500);

            echo 'Dashboard gagal dimuat. Periksa koneksi database, model, dan log PHP.';
        }
    }
}
