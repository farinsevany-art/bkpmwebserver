<?php

class MahasiswaController extends Controller
{
    private const PER_PAGE = 10;

    private MahasiswaRepository $mahasiswaRepository;
    private ProdiRepository $prodiRepository;
    private MahasiswaService $mahasiswaService;

    // Constructor Dependency Injection: dependensi dikirim dari factory.
    public function __construct(
        MahasiswaRepository $mahasiswaRepository,
        ProdiRepository $prodiRepository,
        MahasiswaService $mahasiswaService
    ) {
        $this->mahasiswaRepository = $mahasiswaRepository;
        $this->prodiRepository = $prodiRepository;
        $this->mahasiswaService = $mahasiswaService;
    }

    // Menampilkan daftar mahasiswa, pencarian, dan pagination
    public function index(): void
    {
        if (empty($_SESSION['logged_in'])) {
            $this->redirect('login');
        }

        $keyword = trim($_GET['keyword'] ?? '');

        $total = $this->mahasiswaRepository->total($keyword);
        $totalPages = max(1, (int) ceil($total / self::PER_PAGE));

        $page = max(1, (int) ($_GET['page'] ?? 1));
        $page = min($page, $totalPages);

        $offset = ($page - 1) * self::PER_PAGE;

        $rows = $this->mahasiswaRepository->all(
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

        $row = $this->mahasiswaRepository->find($nim);

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

    // Menyimpan mahasiswa baru; validasi dan logika bisnis ditangani Service.
    public function store(): void
    {
        if (empty($_SESSION['logged_in'])) {
            $this->redirect('login');
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('mahasiswa/create');
        }

        $result = $this->mahasiswaService->create($_POST);

        if (!$result['success']) {
            $this->flashSaveFailure($result['errors'], 'Data mahasiswa gagal disimpan.');
            $this->form('create', null, $result['data'], $result['errors']);
            return;
        }

        $this->flash('success', 'Data mahasiswa berhasil ditambahkan.');
        $this->redirect('mahasiswa');
    }

    // Menampilkan form edit mahasiswa
    public function edit(string $nim): void
    {
        if (empty($_SESSION['logged_in'])) {
            $this->redirect('login');
        }

        $row = $this->mahasiswaRepository->find($nim);

        if (!$row) {
            $this->notFound();
            return;
        }

        $this->form('edit', $nim, $row, []);
    }

    // Memperbarui mahasiswa; validasi dan logika bisnis ditangani Service.
    public function update(string $nim): void
    {
        if (empty($_SESSION['logged_in'])) {
            $this->redirect('login');
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('mahasiswa');
        }

        $result = $this->mahasiswaService->update($nim, $_POST);

        if (!empty($result['not_found'])) {
            $this->notFound();
            return;
        }

        if (!$result['success']) {
            $this->flashSaveFailure($result['errors'], 'Data mahasiswa gagal diubah.');
            $this->form('edit', $nim, $result['data'], $result['errors']);
            return;
        }

        $this->flash('success', 'Data mahasiswa berhasil diperbarui.');
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

        $row = $this->mahasiswaRepository->find($nim);

        if (!$row) {
            $this->notFound();
            return;
        }

        $this->mahasiswaRepository->delete($nim);

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
            'prodiList' => $this->prodiRepository->options(),
        ]);
    }

    private function flashSaveFailure(array $errors, string $failureMessage): void
    {
        if (($errors['nim'] ?? null) === 'NIM sudah terdaftar.') {
            $this->flash('danger', 'NIM sudah terdaftar.');
            return;
        }

        if (isset($errors['general'])) {
            $this->flash('danger', $failureMessage);
        }
    }

}
