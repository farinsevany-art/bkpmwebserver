
<?php

class HomeController extends Controller
{
    private MahasiswaRepository $mahasiswaRepository;
    private Prodi $prodi;
    private MataKuliah $mataKuliah;

    // Constructor Dependency Injection.
    public function __construct(MahasiswaRepository $mahasiswaRepository, Prodi $prodi, MataKuliah $mataKuliah)
    {
        $this->mahasiswaRepository = $mahasiswaRepository;
        $this->prodi = $prodi;
        $this->mataKuliah = $mataKuliah;
    }

    public function dashboard(): void
    {
        // Pastikan hanya administrator yang sudah login
        // dapat membuka dashboard.
        if (empty($_SESSION['logged_in'])) {
            $this->redirect('login');
        }

        try {
            $this->view('dashboard/index', [
                'title' => 'Dashboard',
                'current' => 'dashboard',
                'namaAdmin' => $_SESSION['user_name'] ?? 'Admin',
                'jumlahMahasiswa' => $this->mahasiswaRepository->total(),
                'jumlahProdi' => $this->prodi->total(),
                'jumlahMk' => $this->mataKuliah->total(),
            ]);
        } catch (Throwable $e) {
            error_log('Dashboard gagal dimuat: ' . $e->getMessage());

            http_response_code(500);

            echo 'Dashboard gagal dimuat. Periksa koneksi database, model, dan log PHP.';
        }
    }
}
