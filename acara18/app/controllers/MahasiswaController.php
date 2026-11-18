<?php

class MahasiswaController {
    private Mahasiswa $model;

    public function __construct() {
        $this->model = new Mahasiswa();
    }

    public function index($routeNim = null): void {
        $nim = $_GET['nim'] ?? $_GET['id'] ?? $routeNim;
        if ($nim === null || $nim === '') {
            Response::json(200, true, 'Data berhasil diambil', $this->model->getAll());
        }

        if (!is_string($nim)) {
            Response::json(400, false, 'NIM harus berupa teks', null, [
                'nim' => ['NIM harus berupa teks.']
            ]);
        }

        $data = $this->model->find($nim);
        if (!$data) {
            Response::json(404, false, 'Data mahasiswa tidak ditemukan', null, [
                'nim' => ['Data mahasiswa dengan NIM tersebut tidak ditemukan.']
            ]);
        }

        Response::json(200, true, 'Data berhasil diambil', $data);
    }

    public function store(): void {
        [$data, $errors, $missingRequired, $conflicts] = $this->validateData($this->readJsonBody());

        if ($conflicts !== []) {
            Response::json(409, false, 'Data mahasiswa bertentangan dengan data yang sudah ada', null, $conflicts);
        }

        if ($errors !== []) {
            $status = $missingRequired ? 400 : 422;
            $message = $missingRequired ? 'Field wajib belum lengkap' : 'Data mahasiswa tidak valid';
            Response::json($status, false, $message, null, $errors);
        }

        if (!$this->model->create($data)) {
            Response::json(500, false, 'Data mahasiswa gagal disimpan');
        }

        Response::json(201, true, 'Data berhasil ditambahkan', $data);
    }

    public function update($nim = null): void {
        if (!is_string($nim) || trim($nim) === '') {
            Response::json(400, false, 'NIM mahasiswa wajib disertakan', null, [
                'nim' => ['NIM pada URL wajib diisi.']
            ]);
        }

        $existing = $this->model->find($nim);
        if (!$existing) {
            Response::json(404, false, 'Data mahasiswa tidak ditemukan', null, [
                'nim' => ['Data mahasiswa dengan NIM tersebut tidak ditemukan.']
            ]);
        }

        $input = $this->readJsonBody();
        if ($input === []) {
            Response::json(400, false, 'Body update tidak boleh kosong', null, [
                'body' => ['Kirim minimal satu field untuk diperbarui.']
            ]);
        }

        [$data, $errors, $missingRequired, $conflicts] = $this->validateData(
            array_merge($existing, $input),
            $nim
        );

        if ($conflicts !== []) {
            Response::json(409, false, 'Data mahasiswa bertentangan dengan data yang sudah ada', null, $conflicts);
        }

        if ($errors !== []) {
            $status = $missingRequired ? 400 : 422;
            $message = $missingRequired ? 'Field wajib belum lengkap' : 'Data mahasiswa tidak valid';
            Response::json($status, false, $message, null, $errors);
        }

        if (!$this->model->update($nim, $data)) {
            Response::json(500, false, 'Data mahasiswa gagal diperbarui');
        }

        Response::json(200, true, 'Data berhasil diperbarui', $data);
    }

    public function destroy($nim = null): void {
        if (!is_string($nim) || trim($nim) === '') {
            Response::json(400, false, 'NIM mahasiswa wajib disertakan', null, [
                'nim' => ['NIM pada URL wajib diisi.']
            ]);
        }

        if (!$this->model->find($nim)) {
            Response::json(404, false, 'Data mahasiswa tidak ditemukan', null, [
                'nim' => ['Data mahasiswa dengan NIM tersebut tidak ditemukan.']
            ]);
        }

        if (!$this->model->delete($nim)) {
            Response::json(500, false, 'Data mahasiswa gagal dihapus');
        }

        Response::json(200, true, 'Data berhasil dihapus');
    }

    private function readJsonBody(): array {
        $data = json_decode(file_get_contents('php://input'), true);
        if (!is_array($data) || json_last_error() !== JSON_ERROR_NONE) {
            Response::json(400, false, 'Body JSON tidak valid atau kosong', null, [
                'body' => ['Kirim body berupa JSON object yang valid.']
            ]);
        }

        return $data;
    }

    private function validateData(array $input, ?string $ignoreNim = null): array {
        $errors = [];
        $conflicts = [];
        $missingRequired = false;

        $nimInput = $input['nim'] ?? null;
        $nim = is_string($nimInput) ? trim($nimInput) : '';
        $namaInput = $input['nama'] ?? null;
        $nama = is_string($namaInput) ? trim($namaInput) : '';
        $jenisKelamin = $input['jenis_kelamin'] ?? 'L';
        $alamat = $input['alamat'] ?? '';
        $emailInput = $input['email'] ?? null;
        $email = is_string($emailInput) ? trim($emailInput) : '';
        $idProdi = $input['id_prodi'] ?? null;
        $angkatan = $input['angkatan'] ?? null;
        $validIdProdi = is_int($idProdi) || (is_string($idProdi) && ctype_digit($idProdi));
        $normalizedIdProdi = $validIdProdi ? (int) $idProdi : 0;
        $validAngkatan = is_int($angkatan) || (is_string($angkatan) && ctype_digit($angkatan));
        $normalizedAngkatan = $validAngkatan ? (int) $angkatan : 0;
        $status = $input['status'] ?? 'aktif';
        $foto = $input['foto'] ?? null;

        if ($nimInput === null || $nimInput === '') {
            $errors['nim'][] = 'Field nim wajib diisi.';
            $missingRequired = true;
        } elseif (!is_string($nimInput) || !preg_match('/^[A-Za-z0-9]{9}$/', $nim)) {
            $errors['nim'][] = 'Format NIM tidak valid, harus 9 karakter huruf atau angka.';
        } elseif ($this->model->nimExists($nim, $ignoreNim)) {
            $conflicts['nim'][] = 'NIM sudah terdaftar.';
        }

        if ($namaInput === null || $namaInput === '') {
            $errors['nama'][] = 'Field nama wajib diisi.';
            $missingRequired = true;
        } elseif (!is_string($namaInput)) {
            $errors['nama'][] = 'Nama harus berupa teks.';
        } elseif ($nama === '') {
            $errors['nama'][] = 'Field nama wajib diisi.';
            $missingRequired = true;
        } elseif (mb_strlen($nama) > 100) {
            $errors['nama'][] = 'Nama maksimal 100 karakter.';
        }

        if (!is_string($jenisKelamin) || !in_array($jenisKelamin, ['L', 'P'], true)) {
            $errors['jenis_kelamin'][] = 'Jenis kelamin harus L atau P.';
        }

        if (!is_string($alamat)) {
            $errors['alamat'][] = 'Alamat harus berupa teks.';
        } elseif (mb_strlen($alamat) > 255) {
            $errors['alamat'][] = 'Alamat maksimal 255 karakter.';
        }

        if ($emailInput === null || $emailInput === '') {
            $errors['email'][] = 'Field email wajib diisi.';
            $missingRequired = true;
        } elseif (!is_string($emailInput)) {
            $errors['email'][] = 'Email harus berupa teks.';
        } elseif ($email === '') {
            $errors['email'][] = 'Field email wajib diisi.';
            $missingRequired = true;
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors['email'][] = 'Format email tidak valid.';
        } elseif (strlen($email) > 100) {
            $errors['email'][] = 'Email maksimal 100 karakter.';
        } elseif ($this->model->emailExists($email, $ignoreNim)) {
            $conflicts['email'][] = 'Email sudah digunakan.';
        }

        if ($idProdi === null || $idProdi === '') {
            $errors['id_prodi'][] = 'Field id_prodi wajib diisi dengan ID positif.';
            $missingRequired = true;
        } elseif (!$validIdProdi || $normalizedIdProdi < 1) {
            $errors['id_prodi'][] = 'Field id_prodi harus berupa ID positif.';
        } elseif (!$this->model->prodiExists($normalizedIdProdi)) {
            $errors['id_prodi'][] = 'Program studi tidak ditemukan.';
        }

        if ($angkatan === null || $angkatan === '') {
            $errors['angkatan'][] = 'Field angkatan wajib diisi dengan tahun yang valid.';
            $missingRequired = true;
        } elseif (
            !$validAngkatan
            || $normalizedAngkatan < 2000
            || $normalizedAngkatan > (int) date('Y') + 1
        ) {
            $errors['angkatan'][] = 'Angkatan harus berupa tahun yang valid.';
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
                'id_prodi' => $normalizedIdProdi,
                'angkatan' => $normalizedAngkatan,
                'status' => $status,
                'foto' => $foto,
            ],
            $errors,
            $missingRequired,
            $conflicts,
        ];
    }
}
