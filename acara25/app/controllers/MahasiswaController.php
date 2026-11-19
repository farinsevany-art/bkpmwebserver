<?php

class MahasiswaController {
    private Mahasiswa $model;

    public function __construct() {
        $this->model = new Mahasiswa();
    }

    public function index($nim = null): void {
        Auth::requireAuth();

        $nim = $_GET['nim'] ?? $nim;
        if ($nim !== null && $nim !== '') {
            if (!is_string($nim)) {
                Response::json(400, false, 'NIM harus berupa teks');
            }

            $data = $this->model->find($nim);
            if (!$data) {
                Response::json(404, false, 'Data mahasiswa tidak ditemukan');
            }

            Response::json(200, true, 'Data berhasil diambil', $data);
        }

        Response::json(200, true, 'Data berhasil diambil', $this->model->getAll());
    }

    public function store(): void {
        Auth::requireAdmin();

        [$data, $errors, $missingRequired] = $this->validateData($this->readJsonBody());
        if ($errors !== []) {
            $status = $missingRequired ? 400 : 422;
            $message = $missingRequired ? 'Field wajib belum lengkap' : 'Data mahasiswa tidak valid';
            Response::json($status, false, $message, null, $errors);
        }

        try {
            $nim = $this->model->create($data);
        } catch (PDOException $e) {
            if ($e->getCode() === '23000') {
                Response::json(409, false, 'NIM atau email sudah digunakan');
            }

            error_log('Student create failed: ' . $e->getMessage());
            Response::json(500, false, 'Data mahasiswa gagal disimpan');
        }

        Response::json(201, true, 'Data berhasil ditambahkan', ['nim' => $nim]);
    }

    public function update($nim = null): void {
        Auth::requireAdmin();

        if (!is_string($nim) || trim($nim) === '') {
            Response::json(400, false, 'NIM Mahasiswa wajib diisi');
        }
        $nim = trim($nim);

        $existing = $this->model->find($nim);
        if (!$existing) {
            Response::json(404, false, 'Data mahasiswa tidak ditemukan');
        }

        $input = $this->readJsonBody();
        if ($input === []) {
            Response::json(400, false, 'Body update tidak boleh kosong');
        }

        if (array_key_exists('id_prodi', $input) && !array_key_exists('prodi', $input)) {
            $input['prodi'] = $input['id_prodi'];
        }

        $input = array_merge([
            'nim' => $existing['nim'],
            'nama' => $existing['nama'],
            'jenis_kelamin' => $existing['jenis_kelamin'],
            'alamat' => $existing['alamat'],
            'email' => $existing['email'],
            'prodi' => $existing['id_prodi'],
            'angkatan' => $existing['angkatan'],
            'status' => $existing['status'],
            'foto' => $existing['foto'],
        ], $input);

        [$data, $errors, $missingRequired] = $this->validateData($input, $nim);
        if ($errors !== []) {
            $status = $missingRequired ? 400 : 422;
            $message = $missingRequired ? 'Field wajib belum lengkap' : 'Data mahasiswa tidak valid';
            Response::json($status, false, $message, null, $errors);
        }

        try {
            $updated = $this->model->update($nim, $data);
        } catch (PDOException $e) {
            if ($e->getCode() === '23000') {
                Response::json(409, false, 'NIM atau email sudah digunakan');
            }

            error_log('Student update failed: ' . $e->getMessage());
            Response::json(500, false, 'Data mahasiswa gagal diperbarui');
        }

        if (!$updated) {
            Response::json(500, false, 'Data mahasiswa gagal diperbarui');
        }

        Response::json(200, true, 'Data berhasil diperbarui', $data);
    }

    public function destroy($nim = null): void {
        Auth::requireAdmin();

        if (!is_string($nim) || trim($nim) === '') {
            Response::json(400, false, 'NIM Mahasiswa wajib diisi');
        }
        $nim = trim($nim);

        if (!$this->model->find($nim)) {
            Response::json(404, false, 'Data mahasiswa tidak ditemukan');
        }

        try {
            $deleted = $this->model->delete($nim);
        } catch (PDOException $e) {
            error_log('Student delete failed: ' . $e->getMessage());
            Response::json(500, false, 'Data mahasiswa gagal dihapus');
        }

        if (!$deleted) {
            Response::json(500, false, 'Data mahasiswa gagal dihapus');
        }

        Response::json(200, true, 'Data berhasil dihapus');
    }

    private function readJsonBody(): array {
        $data = json_decode(file_get_contents('php://input'), true);
        if (!is_array($data) || json_last_error() !== JSON_ERROR_NONE) {
            Response::json(400, false, 'Body JSON tidak valid');
        }

        return $data;
    }

    private function validateData(array $input, ?string $ignoreNim = null): array {
        $errors = [];
        $missingRequired = false;

        $nimInput = $input['nim'] ?? null;
        $nim = is_string($nimInput) ? trim($nimInput) : '';
        $namaInput = $input['nama'] ?? null;
        $nama = is_string($namaInput) ? trim($namaInput) : '';
        $jenisKelamin = $input['jenis_kelamin'] ?? 'L';
        $alamat = $input['alamat'] ?? null;
        $email = $input['email'] ?? null;
        $prodiInput = $input['prodi'] ?? $input['id_prodi'] ?? null;
        $validProdi = is_int($prodiInput)
            || (is_string($prodiInput) && ctype_digit($prodiInput));
        $idProdi = $validProdi ? (int) $prodiInput : 0;
        $angkatanInput = $input['angkatan'] ?? null;
        $validAngkatan = is_int($angkatanInput)
            || (is_string($angkatanInput) && ctype_digit($angkatanInput));
        $angkatan = $validAngkatan ? (int) $angkatanInput : 0;
        $status = $input['status'] ?? 'aktif';
        $foto = $input['foto'] ?? null;

        if ($nimInput === null || $nimInput === '') {
            $errors['nim'][] = 'Field nim wajib diisi.';
            $missingRequired = true;
        } elseif (
            !is_string($nimInput)
            || (!preg_match('/^[A-Za-z0-9]{9}$/', $nim) && $nim !== $ignoreNim)
        ) {
            $errors['nim'][] = 'Format NIM tidak valid, harus 9 karakter.';
        } elseif ($this->model->nimExists($nim, $ignoreNim)) {
            $errors['nim'][] = 'NIM sudah terdaftar.';
        }

        if ($namaInput === null || $namaInput === '') {
            $errors['nama'][] = 'Field nama wajib diisi.';
            $missingRequired = true;
        } elseif (!is_string($namaInput) || $nama === '') {
            $errors['nama'][] = 'Nama harus berupa teks dan tidak boleh kosong.';
        } elseif (mb_strlen($nama) > 100) {
            $errors['nama'][] = 'Nama maksimal 100 karakter.';
        }

        if (!is_string($jenisKelamin) || !in_array($jenisKelamin, ['L', 'P'], true)) {
            $errors['jenis_kelamin'][] = 'Jenis kelamin harus L atau P.';
        }

        if ($alamat !== null && !is_string($alamat)) {
            $errors['alamat'][] = 'Alamat harus berupa teks atau null.';
        }

        if ($email === '') {
            $email = null;
        } elseif ($email !== null) {
            if (!is_string($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $errors['email'][] = 'Format email tidak valid.';
            } elseif (strlen($email) > 100) {
                $errors['email'][] = 'Email maksimal 100 karakter.';
            } elseif ($this->model->emailExists($email, $ignoreNim)) {
                $errors['email'][] = 'Email sudah digunakan.';
            }
        }

        if ($prodiInput === null || $prodiInput === '') {
            $errors['prodi'][] = 'Program studi wajib diisi.';
            $missingRequired = true;
        } elseif (!$validProdi || $idProdi < 1 || !$this->model->prodiExists($idProdi)) {
            $errors['prodi'][] = 'Program studi harus berupa ID yang tersedia.';
        }

        if ($angkatanInput === null || $angkatanInput === '') {
            $errors['angkatan'][] = 'Angkatan wajib diisi.';
            $missingRequired = true;
        } elseif (
            !$validAngkatan
            || $angkatan < 2000
            || $angkatan > (int) date('Y') + 1
        ) {
            $errors['angkatan'][] = 'Angkatan tidak valid.';
        }

        if (!is_string($status) || !in_array($status, ['aktif', 'cuti', 'lulus'], true)) {
            $errors['status'][] = 'Status harus aktif, cuti, atau lulus.';
        }

        if ($foto !== null && (!is_string($foto) || strlen($foto) > 255)) {
            $errors['foto'][] = 'Foto harus berupa path teks maksimal 255 karakter atau null.';
        }

        return [
            [
                'nim' => $nim,
                'nama' => $nama,
                'jenis_kelamin' => $jenisKelamin,
                'alamat' => $alamat,
                'email' => $email,
                'id_prodi' => $idProdi,
                'angkatan' => $angkatan,
                'status' => $status,
                'foto' => $foto,
            ],
            $errors,
            $missingRequired,
        ];
    }
}
