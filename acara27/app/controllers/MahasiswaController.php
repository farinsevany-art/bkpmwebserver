<?php

class MahasiswaController {
    private Mahasiswa $model;

    public function __construct() {
        $this->model = new Mahasiswa();
    }

    public function index(): void {
        Auth::requireAuth();

        $nim = $_GET['nim'] ?? null;
        if ($nim !== null && $nim !== '') {
            $data = $this->model->find((string) $nim);
            if (!$data) {
                Response::json(404, false, 'Data tidak ditemukan');
            }
            Response::json(200, true, 'Data berhasil diambil', $data);
        }

        Response::json(200, true, 'Data berhasil diambil', $this->model->getAll());
    }

    public function store(): void {
        Auth::requireAdmin();

        $data = $this->validatedInput();
        $data['foto'] = $this->storeUploadedFoto();

        try {
            $nim = $this->model->create($data);
        } catch (PDOException $e) {
            $this->removeFoto($data['foto']);
            error_log('Mahasiswa create failed: ' . $e->getMessage());
            Response::json(500, false, 'Data mahasiswa gagal disimpan');
        }

        Response::json(201, true, 'Data berhasil ditambahkan', ['nim' => $nim]);
    }

    public function update($nim = null): void {
        Auth::requireAdmin();

        if (!is_string($nim) || $nim === '') {
            Response::json(400, false, 'NIM mahasiswa wajib diisi');
        }
        $existing = $this->model->find($nim);
        if (!$existing) Response::json(404, false, 'Data mahasiswa tidak ditemukan');
        $data = $this->validatedInput($existing);

        $fotoBaru = $this->storeUploadedFoto();
        $data['foto'] = $fotoBaru ?? ($existing['foto'] ?? null);

        try {
            $updated = $this->model->update($nim, $data);
        } catch (PDOException $e) {
            $this->removeFoto($fotoBaru);
            error_log('Mahasiswa update failed: ' . $e->getMessage());
            Response::json(500, false, 'Data mahasiswa gagal diperbarui');
        }

        if (!$updated) {
            $this->removeFoto($fotoBaru);
            Response::json(500, false, 'Data mahasiswa gagal diperbarui');
        }

        if ($fotoBaru !== null) {
            $this->removeFoto($existing['foto'] ?? null);
        }

        Response::json(200, true, 'Data berhasil diperbarui', ['nim' => $data['nim']]);
    }

    public function destroy($nim = null): void {
        Auth::requireAdmin();

        if (!is_string($nim) || $nim === '') {
            Response::json(400, false, 'NIM mahasiswa wajib diisi');
        }

        $existing = $this->model->find($nim);
        if (!$existing) Response::json(404, false, 'Data mahasiswa tidak ditemukan');

        if (!$this->model->delete($nim)) {
            Response::json(500, false, 'Data mahasiswa gagal dihapus');
        }
        $this->removeFoto($existing['foto'] ?? null);
        Response::json(200, true, 'Data berhasil dihapus');
    }

    private function validatedInput(?array $existing = null): array {
        $contentType = $_SERVER['CONTENT_TYPE'] ?? '';
        if (stripos($contentType, 'multipart/form-data') === 0) {
            $data = $_POST;
        } else {
            $data = json_decode(file_get_contents('php://input'), true);
        }

        if (!is_array($data)) {
            Response::json(400, false, 'Body request harus berupa JSON atau form-data yang valid');
        }

        $fields = ['nim', 'nama', 'jenis_kelamin', 'alamat', 'email', 'id_prodi', 'angkatan', 'status'];
        $defaults = $existing === null
            ? ['alamat' => null, 'email' => null, 'status' => 'aktif']
            : array_intersect_key($existing, array_flip($fields));
        $data = array_replace($defaults, $data);

        $errors = [];
        foreach (['nim', 'nama', 'jenis_kelamin'] as $field) {
            if (!isset($data[$field]) || !is_string($data[$field]) || trim($data[$field]) === '') {
                $errors[$field][] = "Field {$field} wajib diisi.";
                continue;
            }
            $data[$field] = trim($data[$field]);
        }

        if (isset($data['nim']) && strlen($data['nim']) > 20) {
            $errors['nim'][] = 'NIM maksimal 20 karakter.';
        }
        if (isset($data['nama']) && strlen($data['nama']) > 100) {
            $errors['nama'][] = 'Nama maksimal 100 karakter.';
        }
        if (isset($data['jenis_kelamin']) && !in_array($data['jenis_kelamin'], ['L', 'P'], true)) {
            $errors['jenis_kelamin'][] = 'Jenis kelamin harus L atau P.';
        }

        foreach (['alamat', 'email'] as $field) {
            if (!array_key_exists($field, $data) || $data[$field] === '') {
                $data[$field] = null;
            } elseif (!is_string($data[$field])) {
                $errors[$field][] = "Field {$field} harus berupa teks.";
                $data[$field] = null;
            } else {
                $data[$field] = trim($data[$field]);
                if ($data[$field] === '') {
                    $data[$field] = null;
                }
            }
        }
        if ($data['email'] !== null && (strlen($data['email']) > 100 || !filter_var($data['email'], FILTER_VALIDATE_EMAIL))) {
            $errors['email'][] = 'Format email tidak valid atau lebih dari 100 karakter.';
        }

        $idProdi = $data['id_prodi'] ?? null;
        $idProdiText = is_int($idProdi) ? (string) $idProdi : $idProdi;
        if (!is_string($idProdiText) || !preg_match('/^[1-9][0-9]*$/', $idProdiText)) {
            $errors['id_prodi'][] = 'ID program studi wajib berupa bilangan bulat positif.';
        } else {
            $data['id_prodi'] = (int) $idProdiText;
        }

        $angkatan = $data['angkatan'] ?? null;
        if (!is_string($angkatan) && !is_int($angkatan)) {
            $errors['angkatan'][] = 'Angkatan wajib diisi dengan tahun 4 digit.';
        } elseif (!preg_match('/^[0-9]{4}$/', (string) $angkatan) || (int) $angkatan < 1901 || (int) $angkatan > 2155) {
            $errors['angkatan'][] = 'Angkatan harus berupa tahun antara 1901 dan 2155.';
        } else {
            $data['angkatan'] = (int) $angkatan;
        }

        if (!isset($data['status']) || !in_array($data['status'], ['aktif', 'cuti', 'lulus'], true)) {
            $errors['status'][] = 'Status harus aktif, cuti, atau lulus.';
        }

        if ($errors) {
            Response::json(422, false, 'Data mahasiswa tidak valid', null, $errors);
        }

        return array_intersect_key($data, array_flip($fields));
    }

    private function storeUploadedFoto(): ?string {
        $file = $_FILES['foto'] ?? null;
        if ($file === null) {
            return null;
        }

        if (!is_array($file) || !isset($file['error']) || !is_int($file['error'])) {
            Response::json(400, false, 'Upload foto gagal');
        }

        if ($file['error'] === UPLOAD_ERR_NO_FILE) {
            return null;
        }
        if (in_array($file['error'], [UPLOAD_ERR_INI_SIZE, UPLOAD_ERR_FORM_SIZE], true)) {
            Response::json(422, false, 'Ukuran berkas maksimal 2MB');
        }
        if (
            $file['error'] !== UPLOAD_ERR_OK
            || !isset($file['name'], $file['tmp_name'], $file['size'])
            || !is_string($file['name'])
            || !is_string($file['tmp_name'])
            || !is_int($file['size'])
            || $file['tmp_name'] === ''
            || !is_uploaded_file($file['tmp_name'])
        ) {
            Response::json(400, false, 'Upload foto gagal');
        }
        if ($file['size'] > 2 * 1024 * 1024) {
            Response::json(422, false, 'Ukuran berkas maksimal 2MB');
        }
        if (!function_exists('exif_imagetype')) {
            error_log('The exif extension is required to validate uploaded photos');
            Response::json(500, false, 'Validasi tipe foto tidak tersedia di server');
        }

        $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        $allowedTypes = [
            'jpg' => IMAGETYPE_JPEG,
            'jpeg' => IMAGETYPE_JPEG,
            'png' => IMAGETYPE_PNG,
        ];
        $imageType = exif_imagetype($file['tmp_name']);
        if (!isset($allowedTypes[$extension]) || $imageType !== $allowedTypes[$extension]) {
            Response::json(422, false, 'Berkas harus berupa gambar JPG/PNG yang valid');
        }

        $storedExtension = $extension === 'jpeg' ? 'jpg' : $extension;
        $filename = bin2hex(random_bytes(16)) . '.' . $storedExtension;
        $uploadDirectory = dirname(__DIR__, 2) . '/public/uploads';
        if (!is_dir($uploadDirectory) && !mkdir($uploadDirectory, 0755, true) && !is_dir($uploadDirectory)) {
            error_log('Unable to create the photo upload directory');
            Response::json(500, false, 'Folder upload foto tidak dapat disiapkan');
        }

        if (!move_uploaded_file($file['tmp_name'], $uploadDirectory . '/' . $filename)) {
            Response::json(500, false, 'Foto gagal disimpan');
        }

        return $filename;
    }

    private function removeFoto(?string $filename): void {
        if ($filename === null || $filename === '' || basename($filename) !== $filename) {
            return;
        }

        $path = dirname(__DIR__, 2) . '/public/uploads/' . $filename;
        if (is_file($path) && !unlink($path)) {
            error_log('Unable to remove uploaded photo: ' . $filename);
        }
    }
}
