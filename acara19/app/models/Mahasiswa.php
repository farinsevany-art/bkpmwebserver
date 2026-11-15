<?php

class Mahasiswa {
    private PDO $db;

    public function __construct() {
        $this->db = Database::getConnection();
    }

    public function getAll(): array {
        $stmt = $this->db->query('SELECT * FROM mahasiswa ORDER BY nim DESC');
        return $stmt->fetchAll();
    }

    public function findByNim(string $nim) {
        $stmt = $this->db->prepare('SELECT * FROM mahasiswa WHERE nim = :nim');
        $stmt->execute([':nim' => $nim]);
        return $stmt->fetch();
    }
}
