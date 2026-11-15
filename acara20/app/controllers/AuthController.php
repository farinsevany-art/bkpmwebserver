<?php

class AuthController {
    private User $model;

    public function __construct() {
        $this->model = new User();
    }

    public function register(): void {
        $data = json_decode(file_get_contents('php://input'), true);

        if (!is_array($data)) {
            Response::json(400, false, 'Body request harus berupa JSON yang valid');
        }

        if (
            !isset($data['username'], $data['email'], $data['password'])
            || !is_string($data['username'])
            || !is_string($data['email'])
            || !is_string($data['password'])
            || trim($data['username']) === ''
            || trim($data['email']) === ''
            || $data['password'] === ''
        ) {
            Response::json(400, false, 'Username, email, dan password wajib diisi');
        }

        $data['username'] = trim($data['username']);
        $data['email'] = trim($data['email']);

        if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            Response::json(422, false, 'Format email tidak valid');
        }

        $data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);

        try {
            $id = $this->model->create($data);
            Response::json(201, true, 'Registrasi berhasil', ['id' => $id]);
        } catch (DomainException $e) {
            Response::json(409, false, $e->getMessage());
        } catch (PDOException $e) {
            error_log('User registration failed: ' . $e->getMessage());
            Response::json(500, false, 'Terjadi kesalahan pada server');
        }
    }

    public function login(): void {
        $data = json_decode(file_get_contents('php://input'), true);

        if (
            !is_array($data)
            || !isset($data['username'], $data['password'])
            || !is_string($data['username'])
            || !is_string($data['password'])
            || trim($data['username']) === ''
            || $data['password'] === ''
        ) {
            Response::json(400, false, 'Username dan password wajib diisi dalam JSON yang valid');
        }

        try {
            $user = $this->model->findByUsername(trim($data['username']));
        } catch (PDOException $e) {
            error_log('User login lookup failed: ' . $e->getMessage());
            Response::json(500, false, 'Terjadi kesalahan pada server');
        }

        if (!$user || !password_verify($data['password'], $user['password'])) {
            Response::json(401, false, 'Username atau password salah');
        }

        $token = Auth::generateToken(['id' => $user['id'], 'role' => $user['role']]);
        Response::json(200, true, 'Login berhasil', ['token' => $token, 'role' => $user['role']]);
    }
}
