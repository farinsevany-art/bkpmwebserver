<?php

/**
 * Repository untuk membaca data program studi yang dibutuhkan mahasiswa.
 */
class ProdiRepository
{
    private PDO $db;

    public function __construct(Database $database)
    {
        $this->db = $database->getConnection();
    }

    public function options(): array
    {
        $stmt = $this->db->query("
            SELECT id_prodi, kode_prodi, nama_prodi, jenjang
            FROM prodi
            ORDER BY nama_prodi ASC
        ");

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function find(int $id): ?array
    {
        $stmt = $this->db->prepare("
            SELECT id_prodi
            FROM prodi
            WHERE id_prodi = :id
        ");
        $stmt->execute(['id' => $id]);

        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }
}
