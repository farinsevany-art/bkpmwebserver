<?php

class MataKuliahController extends Controller
{
    private MataKuliah $mk;
    private Prodi $prodi;

    public function __construct()
    {
        $this->mk = new MataKuliah();
        $this->prodi = new Prodi();
    }

    // Menampilkan daftar mata kuliah
    public function index(): void
    {
        if (empty($_SESSION['logged_in'])) {
            $this->redirect('login');
        }

        $this->view('mata_kuliah/index', [
            'title' => 'Data Mata Kuliah',
            'current' => 'mata-kuliah',
            'rows' => $this->mk->all(),
        ]);
    }

    // Menampilkan form tambah mata kuliah
    public function create(): void
    {
        if (empty($_SESSION['logged_in'])) {
            $this->redirect('login');
        }

        $this->form('create', null, [
            'kode_mk' => '',
            'nama_mk' => '',
            'sks' => 2,
            'semester' => 1,
            'id_prodi' => 0,
        ], []);
    }

    // Menyimpan mata kuliah baru
    public function store(): void
    {
        if (empty($_SESSION['logged_in'])) {
            $this->redirect('login');
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('mata-kuliah/create');
        }

        [$data, $errors] = $this->validate(null);

        if (!empty($errors)) {
            $this->form('create', null, $data, $errors);
            return;
        }

        $this->mk->create($data);

        $this->flash(
            'success',
            'Mata kuliah berhasil ditambahkan.'
        );

        $this->redirect('mata-kuliah');
    }

    // Menampilkan form edit mata kuliah
    public function edit(string $id): void
    {
        if (empty($_SESSION['logged_in'])) {
            $this->redirect('login');
        }

        $id = (int) $id;
        $row = $this->mk->find($id);

        if (!$row) {
            $this->notFound();
            return;
        }

        $this->form('edit', $id, $row, []);
    }

    // Memperbarui mata kuliah
    public function update(string $id): void
    {
        if (empty($_SESSION['logged_in'])) {
            $this->redirect('login');
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('mata-kuliah');
        }

        $id = (int) $id;

        if ($id <= 0 || !$this->mk->find($id)) {
            $this->notFound();
            return;
        }

        [$data, $errors] = $this->validate($id);

        if (!empty($errors)) {
            $this->form('edit', $id, $data, $errors);
            return;
        }

        $this->mk->update($id, $data);

        $this->flash(
            'success',
            'Mata kuliah berhasil diperbarui.'
        );

        $this->redirect('mata-kuliah');
    }

    // Menghapus mata kuliah
    public function destroy(string $id): void
    {
        if (empty($_SESSION['logged_in'])) {
            $this->redirect('login');
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('mata-kuliah');
        }

        $id = (int) $id;

        if ($id <= 0 || !$this->mk->find($id)) {
            $this->notFound();
            return;
        }

        $this->mk->delete($id);

        $this->flash(
            'success',
            'Mata kuliah berhasil dihapus.'
        );

        $this->redirect('mata-kuliah');
    }

    // Menampilkan form tambah atau edit
    private function form(
        string $mode,
        ?int $id,
        array $old,
        array $errors
    ): void {
        $this->view('mata_kuliah/' . $mode, [
            'title' => $mode === 'create'
                ? 'Tambah Mata Kuliah'
                : 'Edit Mata Kuliah',
            'current' => 'mata-kuliah',
            'id' => $id,
            'old' => $old,
            'errors' => $errors,
            'prodiList' => $this->prodi->options(),
        ]);
    }

    // Validasi input mata kuliah
    private function validate(?int $id): array
    {
        $data = [
            'kode_mk' => strtoupper(
                trim($_POST['kode_mk'] ?? '')
            ),
            'nama_mk' => trim($_POST['nama_mk'] ?? ''),
            'sks' => (int) ($_POST['sks'] ?? 0),
            'semester' => (int) ($_POST['semester'] ?? 0),
            'id_prodi' => (int) ($_POST['id_prodi'] ?? 0),
        ];

        $errors = [];

        // Validasi kode mata kuliah
        if ($data['kode_mk'] === '') {
            $errors['kode_mk'] = 'Kode mata kuliah wajib diisi.';
        } elseif (strlen($data['kode_mk']) > 10) {
            $errors['kode_mk'] =
                'Kode mata kuliah maksimal 10 karakter.';
        } elseif (
            $this->mk->kodeExists($data['kode_mk'], $id)
        ) {
            $errors['kode_mk'] =
                'Kode mata kuliah sudah digunakan.';
        }

        // Validasi nama mata kuliah
        if ($data['nama_mk'] === '') {
            $errors['nama_mk'] =
                'Nama mata kuliah wajib diisi.';
        } elseif (mb_strlen($data['nama_mk']) > 100) {
            $errors['nama_mk'] =
                'Nama mata kuliah maksimal 100 karakter.';
        }

        // Validasi SKS
        if ($data['sks'] < 1 || $data['sks'] > 6) {
            $errors['sks'] = 'SKS harus antara 1 dan 6.';
        }

        // Validasi semester
        if (
            $data['semester'] < 1
            || $data['semester'] > 8
        ) {
            $errors['semester'] =
                'Semester harus antara 1 dan 8.';
        }

        // Validasi program studi
        if (
            $data['id_prodi'] <= 0
            || !$this->prodi->find($data['id_prodi'])
        ) {
            $errors['id_prodi'] =
                'Program studi wajib dipilih.';
        }

        return [$data, $errors];
    }
}
