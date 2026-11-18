<?php

class AuthController {
    private User $model;

    public function __construct() {
        $this->model = new User();
    }

    public function register(): void {
        $data = json_decode(file_get_contents('php://input'), true);
        if (!is_array($data) || json_last_error() !== JSON_ERROR_NONE) {
            Response::json(400, false, 'Body JSON tidak valid atau kosong', null, [
                'body' => ['Kirim body berupa JSON object yang valid.']
            ]);
        }

        $errors = [];
        $missingRequired = false;
        $usernameInput = $data['username'] ?? null;
        $emailInput = $data['email'] ?? null;
        $passwordInput = $data['password'] ?? null;

        if ($usernameInput === null || $usernameInput === '') {
            $errors['username'][] = 'Username wajib diisi.';
            $missingRequired = true;
        } elseif (!is_string($usernameInput)) {
            $errors['username'][] = 'Username harus berupa teks.';
        } elseif (trim($usernameInput) === '') {
            $errors['username'][] = 'Username wajib diisi.';
            $missingRequired = true;
        } elseif (strlen(trim($usernameInput)) > 50) {
            $errors['username'][] = 'Username maksimal 50 karakter.';
        }

        if ($emailInput === null || $emailInput === '') {
            $errors['email'][] = 'Email wajib diisi.';
            $missingRequired = true;
        } elseif (!is_string($emailInput)) {
            $errors['email'][] = 'Email harus berupa teks.';
        } elseif (trim($emailInput) === '') {
            $errors['email'][] = 'Email wajib diisi.';
            $missingRequired = true;
        } elseif (strlen(trim($emailInput)) > 100) {
            $errors['email'][] = 'Email maksimal 100 karakter.';
        } elseif (!filter_var(trim($emailInput), FILTER_VALIDATE_EMAIL)) {
            $errors['email'][] = 'Format email tidak valid.';
        }

        if ($passwordInput === null || $passwordInput === '') {
            $errors['password'][] = 'Password wajib diisi.';
            $missingRequired = true;
        } elseif (!is_string($passwordInput)) {
            $errors['password'][] = 'Password harus berupa teks.';
        }

        if ($errors !== []) {
            $status = $missingRequired ? 400 : 422;
            $message = $missingRequired ? 'Username, email, dan password wajib diisi' : 'Data registrasi tidak valid';
            Response::json($status, false, $message, null, $errors);
        }

        $username = trim($usernameInput);
        $email = trim($emailInput);

        $conflicts = [];
        if ($this->model->usernameExists($username)) {
            $conflicts['username'][] = 'Username sudah digunakan.';
        }
        if ($this->model->emailExists($email)) {
            $conflicts['email'][] = 'Email sudah digunakan.';
        }
        if ($conflicts !== []) {
            Response::json(409, false, 'Username atau email sudah terdaftar', null, $conflicts);
        }

        $hashedPassword = password_hash($passwordInput, PASSWORD_DEFAULT);
        if ($hashedPassword === false) {
            Response::json(500, false, 'Password gagal diproses');
        }

        try {
            $id = $this->model->create([
                'username' => $username,
                'email' => $email,
                'password' => $hashedPassword,
            ]);
        } catch (PDOException $e) {
            if ($e->getCode() === '23000') {
                Response::json(409, false, 'Username atau email sudah terdaftar', null, [
                    'account' => ['Username atau email sudah digunakan.']
                ]);
            }

            throw $e;
        }

        Response::json(201, true, 'Registrasi berhasil', ['id' => $id]);
    }
}
