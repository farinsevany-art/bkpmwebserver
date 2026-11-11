<?php

class MahasiswaController extends Controller
{
    private const PER_PAGE = 10;

    private MahasiswaRepository $mahasiswaRepository;
    private Prodi $prodi;
    private MahasiswaService $mahasiswaService;

    // Constructor Dependency Injection: dependensi dikirim dari factory.
    public function __construct(MahasiswaRepository $mahasiswaRepository, Prodi $prodi)
    {
        $this->mahasiswaRepository = $mahasiswaRepository;
        $this->prodi = $prodi;
        $this->mahasiswaService = new MahasiswaService(
            $mahasiswaRepository,
            $prodi
        );
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

        $state = $_SESSION['_mahasiswa_form_state']['create'] ?? null;
        unset($_SESSION['_mahasiswa_form_state']['create']);
        $this->form('create', null, $state['data'] ?? [
            'nim' => '', 'nama' => '', 'jenis_kelamin' => 'L', 'alamat' => '',
            'email' => '', 'id_prodi' => 0, 'angkatan' => (int) date('Y'), 'status' => 'aktif',
        ], $state['errors'] ?? []);
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
            $_SESSION['_mahasiswa_form_state']['create'] = ['data' => $result['data'], 'errors' => $result['errors']];
            $this->flashSaveFailure($result['errors'], 'Data mahasiswa gagal disimpan.');
            $this->redirect('mahasiswa/create');
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

        $state = $_SESSION['_mahasiswa_form_state']['edit'][$nim] ?? null;
        unset($_SESSION['_mahasiswa_form_state']['edit'][$nim]);
        $this->form('edit', $nim, $state['data'] ?? $row, $state['errors'] ?? []);
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

        $row = $this->mahasiswaRepository->find($nim);

        if (!$row) {
            $this->notFound();
            return;
        }

        $result = $this->mahasiswaService->update($nim, $_POST);

        if (!$result['success']) {
            $_SESSION['_mahasiswa_form_state']['edit'][$nim] = ['data' => $result['data'], 'errors' => $result['errors']];
            $this->flashSaveFailure($result['errors'], 'Data mahasiswa gagal diubah.');
            $this->redirect('mahasiswa/' . rawurlencode($nim) . '/edit');
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

        try {
            $deleted = $this->mahasiswaRepository->delete($nim);
            if (!$deleted) { throw new RuntimeException('Operasi hapus mahasiswa tidak berhasil.'); }
        } catch (Throwable $e) {
            AppLogger::error($e, 'MahasiswaController::destroy');
            $this->flash('danger', 'Data mahasiswa gagal dihapus. Silakan coba lagi.');
            $this->redirect('mahasiswa');
        }

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

    private function flashSaveFailure(array $errors, string $failureMessage): void
    {
        if (($errors['nim'] ?? null) === 'NIM sudah terdaftar.') {
            $this->flash('danger', 'NIM sudah terdaftar.');
            return;
        }

        if (isset($errors['general'])) {
            $this->flash('danger', $failureMessage);
            return;
        }

        $this->flash('danger', 'Periksa kembali data yang dimasukkan.');
    }

}
