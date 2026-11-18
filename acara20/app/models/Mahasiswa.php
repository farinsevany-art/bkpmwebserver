<?php

class Mahasiswa {
    private PDO $db;

    public function __construct() {
        $this->db = Database::getConnection();
    }

    public function getAll(): array {
        $stmt = $this->db->query('SELECT * FROM mahasiswa ORDER BY nim DESC');
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function find(string $nim): ?array {
        $stmt = $this->db->prepare('SELECT * FROM mahasiswa WHERE nim = :nim');
        $stmt->execute([':nim' => $nim]);
        $student = $stmt->fetch(PDO::FETCH_ASSOC);

        return $student ?: null;
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
        try {
            $stmt = $this->db->prepare(
                'INSERT INTO mahasiswa
                    (nim, nama, jenis_kelamin, alamat, email, id_prodi, angkatan, status, foto)
                 VALUES
                    (:nim, :nama, :jenis_kelamin, :alamat, :email, :id_prodi, :angkatan, :status, :foto)'
            );
            return $stmt->execute($this->studentParams($data));
        } catch (PDOException $e) {
            if ($e->getCode() === '23000') {
                Response::json(422, false, 'NIM, email, atau program studi bertentangan dengan data yang sudah ada');
            }

            error_log('Mahasiswa create failed: ' . $e->getMessage());
            Response::json(500, false, 'Terjadi kesalahan pada server');
        }
    }

    public function update(string $oldNim, array $data): bool {
        try {
            $stmt = $this->db->prepare(
                'UPDATE mahasiswa
                 SET nim = :nim, nama = :nama, jenis_kelamin = :jenis_kelamin,
                     alamat = :alamat, email = :email, id_prodi = :id_prodi,
                     angkatan = :angkatan, status = :status, foto = :foto
                 WHERE nim = :old_nim'
            );
            return $stmt->execute($this->studentParams($data) + [':old_nim' => $oldNim]);
        } catch (PDOException $e) {
            if ($e->getCode() === '23000') {
                Response::json(422, false, 'NIM, email, atau program studi bertentangan dengan data yang sudah ada');
            }

            error_log('Mahasiswa update failed: ' . $e->getMessage());
            Response::json(500, false, 'Terjadi kesalahan pada server');
        }
    }

    public function delete(string $nim): bool {
        try {
            $stmt = $this->db->prepare('DELETE FROM mahasiswa WHERE nim = :nim');
            return $stmt->execute([':nim' => $nim]);
        } catch (PDOException $e) {
            error_log('Mahasiswa delete failed: ' . $e->getMessage());
            Response::json(500, false, 'Terjadi kesalahan pada server');
        }
    }

    private function studentParams(array $data): array {
        return [
            ':nim' => $data['nim'],
            ':nama' => $data['nama'],
            ':jenis_kelamin' => $data['jenis_kelamin'],
            ':alamat' => $data['alamat'],
            ':email' => $data['email'],
            ':id_prodi' => $data['id_prodi'],
            ':angkatan' => $data['angkatan'],
            ':status' => $data['status'],
            ':foto' => $data['foto'],
        ];
    }
}
