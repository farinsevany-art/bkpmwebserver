<?php

class MahasiswaController {
    private Mahasiswa $model;

    public function __construct() {
        $this->model = new Mahasiswa();
    }

    public function index($routeNim = null): void {
        $nim = $_GET['nim'] ?? $_GET['id'] ?? $routeNim;
        if ($nim !== null && $nim !== '') {
            if (!is_string($nim)) {
                Response::json(400, false, 'NIM harus berupa teks');
            }

            $data = $this->model->find($nim);
            if (!$data) {
                Response::json(404, false, 'Data mahasiswa tidak ditemukan', null, [
                    'nim' => ['Data mahasiswa dengan NIM tersebut tidak ditemukan.']
                ]);
            }
            Response::json(200, true, 'Data berhasil diambil', $data);
        }

        Response::json(200, true, 'Data berhasil diambil', $this->model->getAll());
    }

    public function store(): void {
        Auth::requireAuth();

        $input = $this->readJsonBody();
        [$data, $errors] = $this->validateData($input);

        if ($errors !== []) {
            Response::json(422, false, 'Data mahasiswa tidak valid', null, $errors);
        }

        if (!$this->model->create($data)) {
            Response::json(500, false, 'Data mahasiswa gagal disimpan');
        }

        Response::json(201, true, 'Data berhasil ditambahkan', $data);
    }

    public function update($nim = null): void {
        Auth::requireAuth();

        if (!is_string($nim) || trim($nim) === '') {
            Response::json(400, false, 'NIM Mahasiswa wajib diisi');
        }

        $existing = $this->model->find($nim);
        if (!$existing) Response::json(404, false, 'Data mahasiswa tidak ditemukan');

        $input = $this->readJsonBody();
        [$data, $errors] = $this->validateData(array_merge($existing, $input), $nim);

        if ($errors !== []) {
            Response::json(422, false, 'Data mahasiswa tidak valid', null, $errors);
        }

        if (!$this->model->update($nim, $data)) {
            Response::json(500, false, 'Data mahasiswa gagal diperbarui');
        }

        Response::json(200, true, 'Data berhasil diperbarui', $data);
    }

    public function destroy($nim = null): void {
        Auth::requireAuth();

        if (!is_string($nim) || trim($nim) === '') {
            Response::json(400, false, 'NIM Mahasiswa wajib diisi');
        }
        
        $existing = $this->model->find($nim);
        if (!$existing) Response::json(404, false, 'Data mahasiswa tidak ditemukan');

        $this->model->delete($nim);
        Response::json(200, true, 'Data berhasil dihapus');
    }

    private function readJsonBody(): array {
        $data = json_decode(file_get_contents('php://input'), true);
        if (!is_array($data) || json_last_error() !== JSON_ERROR_NONE) {
            Response::json(400, false, 'Body harus berupa JSON object yang valid');
        }

        return $data;
    }

    private function validateData(array $input, ?string $ignoreNim = null): array {
        $errors = [];
        $nim = is_string($input['nim'] ?? null) ? trim($input['nim']) : '';
        $nama = is_string($input['nama'] ?? null) ? trim($input['nama']) : '';
        $jenisKelamin = $input['jenis_kelamin'] ?? 'L';
        $alamat = $input['alamat'] ?? '';
        $email = is_string($input['email'] ?? null) ? trim($input['email']) : '';
        $idProdi = $input['id_prodi'] ?? null;
        $angkatan = $input['angkatan'] ?? null;
        $validIdProdi = is_int($idProdi) || (is_string($idProdi) && ctype_digit($idProdi));
        $normalizedIdProdi = $validIdProdi ? (int) $idProdi : 0;
        $validAngkatan = is_int($angkatan) || (is_string($angkatan) && ctype_digit($angkatan));
        $normalizedAngkatan = $validAngkatan ? (int) $angkatan : 0;
        $status = $input['status'] ?? 'aktif';
        $foto = $input['foto'] ?? null;

        if ($nim === '') {
            $errors['nim'][] = 'Field nim wajib diisi.';
        } elseif (strlen($nim) !== 9) {
            $errors['nim'][] = 'NIM harus berjumlah 9 karakter.';
        } elseif ($this->model->nimExists($nim, $ignoreNim)) {
            $errors['nim'][] = 'NIM sudah terdaftar.';
        }

        if ($nama === '') {
            $errors['nama'][] = 'Field nama wajib diisi.';
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

        if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors['email'][] = 'Email wajib diisi dengan format yang valid.';
        } elseif (strlen($email) > 100) {
            $errors['email'][] = 'Email maksimal 100 karakter.';
        } elseif ($this->model->emailExists($email, $ignoreNim)) {
            $errors['email'][] = 'Email sudah digunakan.';
        }

        if (
            !$validIdProdi
            || $normalizedIdProdi < 1
            || !$this->model->prodiExists($normalizedIdProdi)
        ) {
            $errors['id_prodi'][] = 'Program studi harus berupa ID yang tersedia.';
        }

        if (
            !$validAngkatan
            || $normalizedAngkatan < 2000
            || $normalizedAngkatan > (int) date('Y') + 1
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
                'id_prodi' => $normalizedIdProdi,
                'angkatan' => $normalizedAngkatan,
                'status' => $status,
                'foto' => $foto,
            ],
            $errors,
        ];
    }
}
