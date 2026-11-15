<?php

class MahasiswaController {
    private Mahasiswa $model;

    public function __construct() {
        $this->model = new Mahasiswa();
    }

    public function index(): void {
        $id = $_GET['id'] ?? null;
        if ($id) {
            $data = $this->model->find((int) $id);
            if (!$data) {
                Response::json(404, false, 'Data mahasiswa tidak ditemukan');
            }
            Response::json(200, true, 'Data berhasil diambil', $data);
        }

        Response::json(200, true, 'Data berhasil diambil', $this->model->getAll());
    }

    public function store(): void {
        Auth::requireAuth();

        $data = $this->validatedInput();
        $id = $this->model->create($data);
        Response::json(201, true, 'Data berhasil ditambahkan', ['id' => $id]);
    }

    public function update(int $id): void {
        Auth::requireAuth();

        if ($id <= 0) {
            Response::json(400, false, 'ID Mahasiswa wajib berupa bilangan positif');
        }

        $data = $this->validatedInput();
        if (!$this->model->find($id)) {
            Response::json(404, false, 'Data mahasiswa tidak ditemukan');
        }

        $result = $this->model->update($id, $data);

        if (!$result) {
            Response::json(500, false, 'Data gagal diperbarui');
        }

        Response::json(200, true, 'Data berhasil diperbarui');
    }

    public function destroy(int $id): void {
        Auth::requireAuth();

        if ($id <= 0) {
            Response::json(400, false, 'ID Mahasiswa wajib berupa bilangan positif');
        }

        if (!$this->model->find($id)) {
            Response::json(404, false, 'Data mahasiswa tidak ditemukan');
        }

        $result = $this->model->delete($id);

        if (!$result) {
            Response::json(500, false, 'Data gagal dihapus');
        }

        Response::json(200, true, 'Data berhasil dihapus');
    }

    private function validatedInput(): array {
        $data = json_decode(file_get_contents('php://input'), true);
        if (!is_array($data)) {
            Response::json(400, false, 'Body request harus berupa JSON yang valid');
        }

        $errors = [];
        foreach (['nim', 'nama', 'prodi'] as $field) {
            if (!isset($data[$field]) || !is_string($data[$field]) || trim($data[$field]) === '') {
                $errors[$field][] = "Field {$field} wajib diisi dengan teks.";
                continue;
            }

            $data[$field] = trim($data[$field]);
        }

        if ($errors) {
            Response::json(400, false, 'Field nim, nama, dan prodi wajib diisi', null, $errors);
        }

        if (strlen($data['nim']) !== 9) {
            Response::json(422, false, 'Format NIM tidak valid, harus 9 karakter');
        }

        return $data;
    }
}
