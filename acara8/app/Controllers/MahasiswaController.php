<?php

class MahasiswaController extends Controller
{
    private const PER_PAGE = 10;

    private Mahasiswa $mahasiswa;
    private Prodi $prodi;

    public function __construct()
    {
        $this->mahasiswa = new Mahasiswa();
        $this->prodi = new Prodi();
    }

    // Menampilkan daftar mahasiswa, pencarian, dan pagination
    public function index(): void
    {
        if (empty($_SESSION['logged_in'])) {
            $this->redirect('login');
        }

        $keyword = trim($_GET['keyword'] ?? '');

        $total = $this->mahasiswa->total($keyword);
        $totalPages = max(1, (int) ceil($total / self::PER_PAGE));

        $page = max(1, (int) ($_GET['page'] ?? 1));
        $page = min($page, $totalPages);

        $offset = ($page - 1) * self::PER_PAGE;

        $rows = $this->mahasiswa->all(
            $keyword,
            self::PER_PAGE,
            $offset
        );

        $this->view('mahasiswa/index', [
            'title' => 'Data Mahasiswa',
            'current' => 'mahasiswa',
            'keyword' => $keyword,
            'rows' => $rows,
            'total' => $total,
            'page' => $page,
            'totalPages' => $totalPages,
            'offset' => $offset,
        ]);
    }

    // Menampilkan detail mahasiswa berdasarkan NIM
    public function show(string $nim): void
    {
        if (empty($_SESSION['logged_in'])) {
            $this->redirect('login');
        }

        $row = $this->mahasiswa->find($nim);

        if (!$row) {
            $this->notFound();
            return;
        }

        $this->view('mahasiswa/show', [
            'title' => 'Detail Mahasiswa',
            'current' => 'mahasiswa',
            'row' => $row,
        ]);
    }

    // Menampilkan form tambah mahasiswa
    public function create(): void
    {
        if (empty($_SESSION['logged_in'])) {
            $this->redirect('login');
        }

        $this->form('create', null, [
            'nim' => '',
            'nama' => '',
            'jenis_kelamin' => 'L',
            'alamat' => '',
            'email' => '',
            'id_prodi' => 0,
            'angkatan' => (int) date('Y'),
            'status' => 'aktif',
        ], []);
    }

    // Menyimpan mahasiswa baru
    public function store(): void
    {
        if (empty($_SESSION['logged_in'])) {
            $this->redirect('login');
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('mahasiswa/create');
        }

        [$data, $errors] = $this->validate(null);

        if (!empty($errors)) {
            $this->form('create', null, $data, $errors);
            return;
        }

        $this->mahasiswa->create($data);

        $this->flash(
            'success',
            'Data mahasiswa berhasil ditambahkan.'
        );

        $this->redirect('mahasiswa');
    }

    // Menampilkan form edit mahasiswa
    public function edit(string $nim): void
    {
        if (empty($_SESSION['logged_in'])) {
            $this->redirect('login');
        }

        $row = $this->mahasiswa->find($nim);

        if (!$row) {
            $this->notFound();
            return;
        }

        $this->form('edit', $nim, $row, []);
    }

    // Memperbarui data mahasiswa
    public function update(string $nim): void
    {
        if (empty($_SESSION['logged_in'])) {
            $this->redirect('login');
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('mahasiswa');
        }

        $row = $this->mahasiswa->find($nim);

        if (!$row) {
            $this->notFound();
            return;
        }

        [$data, $errors] = $this->validate($nim);

        if (!empty($errors)) {
            $this->form('edit', $nim, $data, $errors);
            return;
        }

        $this->mahasiswa->update($nim, $data);

        $this->flash(
            'success',
            'Data mahasiswa berhasil diperbarui.'
        );

        $this->redirect('mahasiswa');
    }

    // Menghapus mahasiswa berdasarkan NIM
    public function destroy(string $nim): void
    {
        if (empty($_SESSION['logged_in'])) {
            $this->redirect('login');
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('mahasiswa');
        }

        $row = $this->mahasiswa->find($nim);

        if (!$row) {
            $this->notFound();
            return;
        }

        $this->mahasiswa->delete($nim);

        $this->flash(
            'success',
            'Data mahasiswa berhasil dihapus.'
        );

        $this->redirect('mahasiswa');
    }

    // Menampilkan form tambah atau edit
    private function form(
        string $mode,
        ?string $nim,
        array $old,
        array $errors
    ): void {
        $this->view('mahasiswa/' . $mode, [
            'title' => $mode === 'create'
                ? 'Tambah Mahasiswa'
                : 'Edit Mahasiswa',
            'current' => 'mahasiswa',
            'id' => $nim,
            'old' => $old,
            'errors' => $errors,
            'prodiList' => $this->prodi->options(),
        ]);
    }

    // Validasi data mahasiswa
    private function validate(?string $nimLama): array
    {
        $data = [
            'nim' => trim($_POST['nim'] ?? ''),
            'nama' => trim($_POST['nama'] ?? ''),
            'jenis_kelamin' => $_POST['jenis_kelamin'] ?? 'L',
            'alamat' => trim($_POST['alamat'] ?? ''),
            'email' => trim($_POST['email'] ?? ''),
            'id_prodi' => (int) ($_POST['id_prodi'] ?? 0),
            'angkatan' => (int) ($_POST['angkatan'] ?? 0),
            'status' => $_POST['status'] ?? 'aktif',
        ];

        $errors = [];

        // Validasi NIM
        if ($data['nim'] === '') {
            $errors['nim'] = 'NIM wajib diisi.';
        } elseif (strlen($data['nim']) > 20) {
            $errors['nim'] = 'NIM maksimal 20 karakter.';
        } elseif (
            $this->mahasiswa->nimExists(
                $data['nim'],
                $nimLama
            )
        ) {
            $errors['nim'] = 'NIM sudah terdaftar.';
        }

        // Validasi nama
        if ($data['nama'] === '') {
            $errors['nama'] = 'Nama wajib diisi.';
        } elseif (mb_strlen($data['nama']) > 100) {
            $errors['nama'] = 'Nama maksimal 100 karakter.';
        }

        // Validasi email
        if (
            $data['email'] === ''
            || !filter_var(
                $data['email'],
                FILTER_VALIDATE_EMAIL
            )
        ) {
            $errors['email'] = 'Format email tidak valid.';
        } elseif (strlen($data['email']) > 100) {
            $errors['email'] = 'Email maksimal 100 karakter.';
        } elseif (
            $this->mahasiswa->emailExists(
                $data['email'],
                $nimLama
            )
        ) {
            $errors['email'] = 'Email sudah digunakan.';
        }

        // Validasi jenis kelamin
        if (
            !in_array(
                $data['jenis_kelamin'],
                ['L', 'P'],
                true
            )
        ) {
            $errors['jenis_kelamin'] =
                'Jenis kelamin tidak valid.';
        }

        // Validasi program studi
        if (
            $data['id_prodi'] <= 0
            || !$this->prodi->find($data['id_prodi'])
        ) {
            $errors['id_prodi'] =
                'Program studi wajib dipilih.';
        }

        // Validasi status mahasiswa
        if (
            !in_array(
                $data['status'],
                ['aktif', 'cuti', 'lulus'],
                true
            )
        ) {
            $errors['status'] =
                'Status mahasiswa tidak valid.';
        }

        // Validasi tahun angkatan
        $maxYear = (int) date('Y') + 1;

        if (
            $data['angkatan'] < 2000
            || $data['angkatan'] > $maxYear
        ) {
            $errors['angkatan'] =
                "Angkatan harus antara 2000 dan {$maxYear}.";
        }

        // Validasi panjang alamat
        if (mb_strlen($data['alamat']) > 255) {
            $errors['alamat'] =
                'Alamat maksimal 255 karakter.';
        }

        return [$data, $errors];
    }
}
