<?php

class Mahasiswa {
    private PDO $db;

    public function __construct() {
        $this->db = Database::getConnection();
    }

    public function getAll(): array {
        $stmt = $this->db->query("SELECT * FROM mahasiswa");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function find(int $id) {
        $stmt = $this->db->prepare("SELECT * FROM mahasiswa WHERE id = :id");
        $stmt->execute([':id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function create(array $data): string {
        try {
            $stmt = $this->db->prepare(
                'INSERT INTO mahasiswa (nim, nama, prodi) VALUES (:nim, :nama, :prodi)'
            );
            $stmt->execute([
                ':nim' => $data['nim'],
                ':nama' => $data['nama'],
                ':prodi' => $data['prodi']
            ]);
            return $this->db->lastInsertId();
        } catch (PDOException $e) {
            Response::json(500, false, 'Terjadi kesalahan pada server');
        }
    }

    public function update(int $id, array $data): bool {
        try {
            $stmt = $this->db->prepare(
                'UPDATE mahasiswa SET nim = :nim, nama = :nama, prodi = :prodi WHERE id = :id'
            );
            return $stmt->execute([
                ':id' => $id,
                ':nim' => $data['nim'],
                ':nama' => $data['nama'],
                ':prodi' => $data['prodi']
            ]);
        } catch (PDOException $e) {
            Response::json(500, false, 'Terjadi kesalahan pada server');
        }
    }

    public function delete(int $id): bool {
        try {
            $stmt = $this->db->prepare('DELETE FROM mahasiswa WHERE id = :id');
            return $stmt->execute([':id' => $id]);
        } catch (PDOException $e) {
            Response::json(500, false, 'Terjadi kesalahan pada server');
        }
    }
}
