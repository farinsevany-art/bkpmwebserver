<?php

class MahasiswaController {
    private Mahasiswa $model;

    public function __construct() {
        $this->model = new Mahasiswa();
    }

    public function index(): void {
        $nim = trim((string) ($_GET['nim'] ?? ''));

        if ($nim !== '') {
            $data = $this->model->findByNim($nim);
            if (!$data) {
                Response::json(404, false, 'Data mahasiswa tidak ditemukan');
            }

            Response::json(200, true, 'Data berhasil diambil', $data);
        }

        Response::json(200, true, 'Data berhasil diambil', $this->model->getAll());
    }
}
