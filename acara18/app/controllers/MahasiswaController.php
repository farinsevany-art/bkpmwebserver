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
                Response::json(404, false, 'Data tidak ditemukan');
            }
            Response::json(200, true, 'Data berhasil diambil', $data);
        }

        Response::json(200, true, 'Data berhasil diambil', $this->model->getAll());
    }

    public function store(): void {
        $data = json_decode(file_get_contents('php://input'), true);
        $errors = [];

        if (empty($data['nim'])) {
            $errors['nim'][] = 'Field nim wajib diisi.';
        }
        if (empty($data['nama'])) {
            $errors['nama'][] = 'Field nama wajib diisi.';
        }
        if (empty($data['prodi'])) {
            $errors['prodi'][] = 'Field prodi wajib diisi.';
        }

        if (!empty($errors)) {
            Response::json(400, false, 'Field nim, nama, dan prodi wajib diisi', null, $errors);
        }

        if (strlen($data['nim']) !== 9) {
            $errors['nim'][] = 'Format NIM tidak valid, harus 9 digit.';
            Response::json(422, false, 'Format NIM tidak valid, harus 9 digit', null, $errors);
        }

        $id = $this->model->create($data);
        Response::json(201, true, 'Data berhasil ditambahkan', ['id' => $id]);
    }
}
