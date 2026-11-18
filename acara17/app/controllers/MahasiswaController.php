<?php
class MahasiswaController {
    private Mahasiswa $model;

    public function __construct() {
        $this->model = new Mahasiswa();
    }

    public function index($routeNim = null): void {
        header('Content-Type: application/json');
        $nim = $_GET['nim'] ?? $_GET['id'] ?? $routeNim;
        
        if ($nim !== null && $nim !== '') {
            if (!is_string($nim)) {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'NIM harus berupa teks']);
                exit;
            }

            $data = $this->model->find($nim);
            if (!$data) {
                echo json_encode(['success' => true, 'data' => []]);
                exit;
            }
            echo json_encode(['success' => true, 'data' => $data]);
            exit;
        }
        
        echo json_encode(['success' => true, 'data' => $this->model->getAll()]);
    }

    public function store(): void {
        header('Content-Type: application/json');
        $input = $this->readJsonBody();
        [$data, $errors] = $this->validateData($input);

        if ($errors !== []) {
            http_response_code(422);
            echo json_encode(['success' => false, 'message' => 'Data mahasiswa tidak valid', 'errors' => $errors]);
            exit;
        }

        if (!$this->model->create($data)) {
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => 'Data mahasiswa gagal disimpan']);
            exit;
        }

        http_response_code(201);
        echo json_encode([
            'success' => true,
            'message' => 'Data mahasiswa berhasil ditambahkan',
            'data' => $data
        ]);
    }

    public function update($nim): void {
        header('Content-Type: application/json');
        if (!is_string($nim) || trim($nim) === '') {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'NIM mahasiswa wajib disertakan']);
            exit;
        }

        $existing = $this->model->find($nim);
        if (!$existing) {
            http_response_code(404);
            echo json_encode(['success' => false, 'message' => 'Data mahasiswa tidak ditemukan untuk diperbarui']);
            exit;
        }

        $input = $this->readJsonBody();
        [$data, $errors] = $this->validateData(array_merge($existing, $input), $nim);
        if ($errors !== []) {
            http_response_code(422);
            echo json_encode(['success' => false, 'message' => 'Data mahasiswa tidak valid', 'errors' => $errors]);
            exit;
        }

        if (!$this->model->update($nim, $data)) {
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => 'Data mahasiswa gagal diperbarui']);
            exit;
        }

        echo json_encode(['success' => true, 'message' => 'Data mahasiswa berhasil diperbarui', 'data' => $data]);
    }

    public function destroy($nim): void {
        header('Content-Type: application/json');
        if (!is_string($nim) || $nim === '') {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'NIM mahasiswa wajib disertakan']);
            exit;
        }

        $existing = $this->model->find($nim);
        if (!$existing) {
            http_response_code(404);
            echo json_encode(['success' => false, 'message' => 'Data mahasiswa tidak ditemukan untuk dihapus']);
            exit;
        }

        $this->model->delete($nim);
        echo json_encode(['success' => true, 'message' => 'Data mahasiswa berhasil dihapus']);
    }

    private function readJsonBody(): array {
        $data = json_decode(file_get_contents('php://input'), true);
        if (!is_array($data) || json_last_error() !== JSON_ERROR_NONE) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Body harus berupa JSON object yang valid']);
            exit;
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
        } elseif ($this->model->isNimExists($nim, $ignoreNim)) {
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
        } elseif ($this->model->isEmailExists($email, $ignoreNim)) {
            $errors['email'][] = 'Email sudah digunakan.';
        }

        if (
            !$validIdProdi
            || $normalizedIdProdi < 1
            || !$this->model->isProdiExists($normalizedIdProdi)
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
?>