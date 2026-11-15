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

    public function find(string $nim) {
        $stmt = $this->db->prepare('SELECT * FROM mahasiswa WHERE nim = :nim');
        $stmt->execute([':nim' => $nim]);
        return $stmt->fetch();
    }

    public function create(array $data): string {
        $stmt = $this->db->prepare(
            'INSERT INTO mahasiswa
                (nim, nama, jenis_kelamin, alamat, email, id_prodi, angkatan, status, foto)
             VALUES
                (:nim, :nama, :jenis_kelamin, :alamat, :email, :id_prodi, :angkatan, :status, :foto)'
        );
        $stmt->execute([
            ':nim' => $data['nim'],
            ':nama' => $data['nama'],
            ':jenis_kelamin' => $data['jenis_kelamin'],
            ':alamat' => $data['alamat'],
            ':email' => $data['email'],
            ':id_prodi' => $data['id_prodi'],
            ':angkatan' => $data['angkatan'],
            ':status' => $data['status'],
            ':foto' => $data['foto'] ?? null
        ]);
        return $data['nim'];
    }

    public function update(string $nim, array $data): bool {
        $stmt = $this->db->prepare(
            'UPDATE mahasiswa
             SET nim = :nim, nama = :nama, jenis_kelamin = :jenis_kelamin,
                 alamat = :alamat, email = :email, id_prodi = :id_prodi,
                 angkatan = :angkatan, status = :status, foto = :foto
             WHERE nim = :old_nim'
        );
        return $stmt->execute([
            ':old_nim' => $nim,
            ':nim' => $data['nim'],
            ':nama' => $data['nama'],
            ':jenis_kelamin' => $data['jenis_kelamin'],
            ':alamat' => $data['alamat'],
            ':email' => $data['email'],
            ':id_prodi' => $data['id_prodi'],
            ':angkatan' => $data['angkatan'],
            ':status' => $data['status'],
            ':foto' => $data['foto'] ?? null
        ]);
    }

    public function delete(string $nim): bool {
        $stmt = $this->db->prepare('DELETE FROM mahasiswa WHERE nim = :nim');
        return $stmt->execute([':nim' => $nim]);
    }
}
