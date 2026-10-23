<?php
class MahasiswaController extends Controller
{
    private function getData(): array
    {
        if (!isset($_SESSION['mahasiswa'])) {
            $_SESSION['mahasiswa'] = [
                ['nim' => 'E41240001', 'nama' => 'Andi Pratama', 'prodi' => 'Manajemen Informatika'],
                ['nim' => 'E41240002', 'nama' => 'Siti Aminah', 'prodi' => 'Teknik Informatika'],
            ];
        }
        return $_SESSION['mahasiswa'];
    }

    public function index(): void
    {
        $mahasiswa = $this->getData();
        $flash = $_SESSION['flash'] ?? null;
        unset($_SESSION['flash']);
        $this->view('mahasiswa/index', ['mahasiswa' => $mahasiswa, 'flash' => $flash]);
    }

    public function create(): void
    {
        $this->view('mahasiswa/create', ['flash' => null]);
    }

    public function store(): void
    {
        $nim = trim($_POST['nim'] ?? '');
        $nama = trim($_POST['nama'] ?? '');
        $prodi = trim($_POST['prodi'] ?? '');

        if ($nim === '' || $nama === '' || $prodi === '') {
            $this->view('mahasiswa/create', [
                'flash' => ['type' => 'error', 'message' => 'Semua kolom wajib diisi.'],
            ]);
            return;
        }

        $mahasiswa = $this->getData();
        foreach ($mahasiswa as $item) {
            if ($item['nim'] === $nim) {
                $this->view('mahasiswa/create', [
                    'flash' => ['type' => 'error', 'message' => 'NIM sudah terdaftar.'],
                ]);
                return;
            }
        }

        $mahasiswa[] = ['nim' => $nim, 'nama' => $nama, 'prodi' => $prodi];
        $_SESSION['mahasiswa'] = $mahasiswa;
        $_SESSION['flash'] = ['type' => 'success', 'message' => 'Data mahasiswa berhasil ditambahkan.'];
        $this->redirect('mahasiswa');
    }
}
