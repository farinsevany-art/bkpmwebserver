<?php

class AuthController {
    private User $model;

    public function __construct() {
        $this->model = new User();
    }

    public function register(): void {
        $data = json_decode(file_get_contents('php://input'), true);

        if (empty($data['username']) || empty($data['email']) || empty($data['password'])) {
            Response::json(400, false, 'Username, email, dan password wajib diisi');
        }

        if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            Response::json(422, false, 'Format email tidak valid');
        }

        $data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);

        try {
            $id = $this->model->create($data);
            Response::json(201, true, 'Registrasi berhasil', ['id' => $id]);
        } catch (Exception $e) {
            Response::json(400, false, $e->getMessage());
        }
    }
}
