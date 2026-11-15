<?php

class User {
    private PDO $db;

    public function __construct() {
        $this->db = Database::getConnection();
    }

    public function create(array $data): string {
        $stmt = $this->db->prepare(
            'INSERT INTO users (username, email, password) VALUES (:username, :email, :password)'
        );

        try {
            $stmt->execute([
                ':username' => $data['username'],
                ':email' => $data['email'],
                ':password' => $data['password'],
            ]);
        } catch (PDOException $e) {
            if ($e->getCode() === '23000') {
                throw new DomainException('Username atau email sudah terdaftar');
            }

            throw $e;
        }

        return $this->db->lastInsertId();
    }

    public function findByUsername(string $username): ?array {
        $stmt = $this->db->prepare('SELECT id, username, email, password, role FROM users WHERE username = :username');
        $stmt->execute([':username' => $username]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        return $user ?: null;
    }
}
