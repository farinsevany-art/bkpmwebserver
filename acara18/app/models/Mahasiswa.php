<?php

class Mahasiswa {
    private PDO $db;

    public function __construct() {
        $this->db = Database::getConnection();
    }

    public function getAll(): array {
        $stmt = $this->db->query("SELECT * FROM mahasiswa");
        return $stmt->fetchAll();
    }

    public function find(int $id) {
        $stmt = $this->db->prepare("SELECT * FROM mahasiswa WHERE id = :id");
        $stmt->execute([':id' => $id]);
        return $stmt->fetch();
    }

    public function create(array $data): string {
        $stmt = $this->db->prepare(
            'INSERT INTO mahasiswa (nim, nama, prodi) VALUES (:nim, :nama, :prodi)'
        );
        $stmt->execute([
            ':nim' => $data['nim'],
            ':nama' => $data['nama'],
            ':prodi' => $data['prodi']
        ]);
        return $this->db->lastInsertId();
    }
}
