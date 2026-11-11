<?php
namespace App\Models;

use App\Core\Database;
use PDO;

class Mahasiswa {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance();
    }

    public function getAll() {
        $stmt = $this->db->query("SELECT * FROM mahasiswa ORDER BY nim ASC");
        return $stmt->fetchAll();
    }

    public function getById($id) {
        $stmt = $this->db->prepare("SELECT * FROM mahasiswa WHERE id = :id");
        $stmt->execute([':id' => $id]);
        return $stmt->fetch();
    }

    public function getByNim($nim) {
        $stmt = $this->db->prepare("SELECT * FROM mahasiswa WHERE nim = :nim");
        $stmt->execute([':nim' => $nim]);
        return $stmt->fetch();
    }

    public function create($data) {
        $query = "INSERT INTO mahasiswa (nim, nama, jenis_kelamin, alamat, email, id_prodi, angkatan, status) 
                  VALUES (:nim, :nama, :jenis_kelamin, :alamat, :email, :id_prodi, :angkatan, :status)";
        $stmt = $this->db->prepare($query);
        return $stmt->execute([
            ':nim' => $data['nim'],
            ':nama' => $data['nama'],
            ':jenis_kelamin' => $data['jenis_kelamin'] ?? 'L',
            ':alamat' => $data['alamat'] ?? '',
            ':email' => $data['email'],
            ':id_prodi' => $data['id_prodi'] ?? 1,
            ':angkatan' => $data['angkatan'] ?? date('Y'),
            ':status' => $data['status'] ?? 'aktif'
        ]);
    }
}
