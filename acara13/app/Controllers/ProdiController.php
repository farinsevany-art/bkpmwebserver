<?php

class ProdiController extends Controller
{
    private Prodi $prodi;

    public function __construct()
    {
        $this->prodi = new Prodi();
    }

    // Menampilkan daftar program studi
    public function index(): void
    {
        if (empty($_SESSION['logged_in'])) {
            $this->redirect('login');
        }

        $this->view('prodi/index', [
            'title' => 'Data Program Studi',
            'current' => 'prodi',
            'rows' => $this->prodi->all(),
        ]);
    }

    // Menampilkan form tambah program studi
    public function create(): void
    {
        if (empty($_SESSION['logged_in'])) {
            $this->redirect('login');
        }

        $this->form('create', null, [
            'kode_prodi' => '',
            'nama_prodi' => '',
            'jenjang' => 'D3',
        ], []);
    }

    // Menyimpan program studi baru
    public function store(): void
    {
        if (empty($_SESSION['logged_in'])) {
            $this->redirect('login');
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('prodi/create');
        }

        [$data, $errors] = $this->validate(null);

        if (!empty($errors)) {
            $this->form('create', null, $data, $errors);
            return;
        }

        $this->prodi->create($data);

        $this->flash(
            'success',
            'Program studi berhasil ditambahkan.'
        );

        $this->redirect('prodi');
    }

    // Menampilkan form edit program studi
    public function edit(string $id): void
    {
        if (empty($_SESSION['logged_in'])) {
            $this->redirect('login');
        }

        $id = (int) $id;
        $row = $this->prodi->find($id);

        if (!$row) {
            $this->notFound();
            return;
        }

        $this->form('edit', $id, $row, []);
    }

    // Memperbarui program studi
    public function update(string $id): void
    {
        if (empty($_SESSION['logged_in'])) {
            $this->redirect('login');
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('prodi');
        }

        $id = (int) $id;

        if ($id <= 0 || !$this->prodi->find($id)) {
            $this->notFound();
            return;
        }

        [$data, $errors] = $this->validate($id);

        if (!empty($errors)) {
            $this->form('edit', $id, $data, $errors);
            return;
        }

        $this->prodi->update($id, $data);

        $this->flash(
            'success',
            'Program studi berhasil diperbarui.'
        );

        $this->redirect('prodi');
    }

    // Menghapus program studi
    public function destroy(string $id): void
    {
        if (empty($_SESSION['logged_in'])) {
            $this->redirect('login');
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('prodi');
        }

        $id = (int) $id;

        if ($id <= 0 || !$this->prodi->find($id)) {
            $this->notFound();
            return;
        }

        try {
            $this->prodi->delete($id);

            $this->flash(
                'success',
                'Program studi berhasil dihapus.'
            );
        } catch (PDOException $e) {
            // Program studi masih digunakan oleh mahasiswa atau mata kuliah
            if ($e->getCode() === '23000') {
                $this->flash(
                    'danger',
                    'Program studi tidak bisa dihapus karena masih digunakan oleh mahasiswa atau mata kuliah.'
                );
            } else {
                error_log(
                    'Gagal menghapus program studi: '
                    . $e->getMessage()
                );

                $this->flash(
                    'danger',
                    'Terjadi kesalahan saat menghapus program studi.'
                );
            }
        }

        $this->redirect('prodi');
    }

    // Menampilkan form tambah atau edit
    private function form(
        string $mode,
        ?int $id,
        array $old,
        array $errors
    ): void {
        $this->view('prodi/' . $mode, [
            'title' => $mode === 'create'
                ? 'Tambah Program Studi'
                : 'Edit Program Studi',
            'current' => 'prodi',
            'id' => $id,
            'old' => $old,
            'errors' => $errors,
        ]);
    }

    // Validasi input program studi
    private function validate(?int $id): array
    {
        $data = [
            'kode_prodi' => strtoupper(
                trim($_POST['kode_prodi'] ?? '')
            ),
            'nama_prodi' => trim(
                $_POST['nama_prodi'] ?? ''
            ),
            'jenjang' => strtoupper(
                trim($_POST['jenjang'] ?? '')
            ),
        ];

        $errors = [];

        // Validasi kode program studi
        if ($data['kode_prodi'] === '') {
            $errors['kode_prodi'] =
                'Kode program studi wajib diisi.';
        } elseif (strlen($data['kode_prodi']) > 10) {
            $errors['kode_prodi'] =
                'Kode maksimal 10 karakter.';
        } elseif (
            $this->prodi->kodeExists(
                $data['kode_prodi'],
                $id
            )
        ) {
            $errors['kode_prodi'] =
                'Kode program studi sudah digunakan.';
        }

        // Validasi nama program studi
        if ($data['nama_prodi'] === '') {
            $errors['nama_prodi'] =
                'Nama program studi wajib diisi.';
        } elseif (mb_strlen($data['nama_prodi']) > 100) {
            $errors['nama_prodi'] =
                'Nama maksimal 100 karakter.';
        }

        // Validasi jenjang
        if (
            !in_array(
                $data['jenjang'],
                ['D3', 'D4', 'S1', 'S2', 'S3'],
                true
            )
        ) {
            $errors['jenjang'] =
                'Jenjang harus dipilih dengan benar.';
        }

        return [$data, $errors];
    }
}
