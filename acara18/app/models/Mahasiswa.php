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

    public function find(string $nim) {
        $stmt = $this->db->prepare('SELECT * FROM mahasiswa WHERE nim = :nim');
        $stmt->execute([':nim' => $nim]);
        return $stmt->fetch();
    }

    public function nimExists(string $nim, ?string $ignoreNim = null): bool {
        $sql = 'SELECT COUNT(*) FROM mahasiswa WHERE nim = :nim';
        $params = [':nim' => $nim];

        if ($ignoreNim !== null) {
            $sql .= ' AND nim <> :ignore_nim';
            $params[':ignore_nim'] = $ignoreNim;
        }

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return (int) $stmt->fetchColumn() > 0;
    }

    public function emailExists(string $email, ?string $ignoreNim = null): bool {
        $sql = 'SELECT COUNT(*) FROM mahasiswa WHERE email = :email';
        $params = [':email' => $email];

        if ($ignoreNim !== null) {
            $sql .= ' AND nim <> :ignore_nim';
            $params[':ignore_nim'] = $ignoreNim;
        }

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return (int) $stmt->fetchColumn() > 0;
    }

    public function prodiExists(int $idProdi): bool {
        $stmt = $this->db->prepare('SELECT COUNT(*) FROM prodi WHERE id_prodi = :id_prodi');
        $stmt->execute([':id_prodi' => $idProdi]);
        return (int) $stmt->fetchColumn() > 0;
    }

    public function create(array $data): bool {
        $stmt = $this->db->prepare(
            'INSERT INTO mahasiswa
                (nim, nama, jenis_kelamin, alamat, email, id_prodi, angkatan, status, foto)
             VALUES
                (:nim, :nama, :jenis_kelamin, :alamat, :email, :id_prodi, :angkatan, :status, :foto)'
        );
        return $stmt->execute([
            ':nim' => $data['nim'],
            ':nama' => $data['nama'],
            ':jenis_kelamin' => $data['jenis_kelamin'],
            ':alamat' => $data['alamat'],
            ':email' => $data['email'],
            ':id_prodi' => $data['id_prodi'],
            ':angkatan' => $data['angkatan'],
            ':status' => $data['status'],
            ':foto' => $data['foto']
        ]);
    }

    public function update(string $oldNim, array $data): bool {
        $stmt = $this->db->prepare(
            'UPDATE mahasiswa
             SET nim = :nim, nama = :nama, jenis_kelamin = :jenis_kelamin,
                 alamat = :alamat, email = :email, id_prodi = :id_prodi,
                 angkatan = :angkatan, status = :status, foto = :foto
             WHERE nim = :old_nim'
        );
        return $stmt->execute([
            ':old_nim' => $oldNim,
            ':nim' => $data['nim'],
            ':nama' => $data['nama'],
            ':jenis_kelamin' => $data['jenis_kelamin'],
            ':alamat' => $data['alamat'],
            ':email' => $data['email'],
            ':id_prodi' => $data['id_prodi'],
            ':angkatan' => $data['angkatan'],
            ':status' => $data['status'],
            ':foto' => $data['foto']
        ]);
    }

    public function delete(string $nim): bool {
        $stmt = $this->db->prepare('DELETE FROM mahasiswa WHERE nim = :nim');
        return $stmt->execute([':nim' => $nim]);
    }
}
