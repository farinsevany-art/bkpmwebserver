<?php
class Buku {
    private PDO $db;

    public function __construct() {
        $this->db = Database::getConnection();
    }

    public function getAll(): array {
        return $this->db->query('SELECT * FROM buku ORDER BY id DESC')->fetchAll();
    }

    public function find(int $id) {
        $stmt = $this->db->prepare('SELECT * FROM buku WHERE id = :id');
        $stmt->execute([':id' => $id]);
        return $stmt->fetch();
    }

    public function create(array $data): string {
        $stmt = $this->db->prepare(
            'INSERT INTO buku (judul, pengarang, penerbit, tahun_terbit) VALUES (:judul, :pengarang, :penerbit, :tahun_terbit)'
        );
        $stmt->execute([
            ':judul' => $data['judul'], 
            ':pengarang' => $data['pengarang'], 
            ':penerbit' => $data['penerbit'],
            ':tahun_terbit' => $data['tahun_terbit']
        ]);
        return $this->db->lastInsertId();
    }

    public function update(int $id, array $data): void {
        $stmt = $this->db->prepare(
            'UPDATE buku SET judul = :judul, pengarang = :pengarang, penerbit = :penerbit, tahun_terbit = :tahun_terbit WHERE id = :id'
        );
        $stmt->execute([
            ':judul' => $data['judul'], 
            ':pengarang' => $data['pengarang'], 
            ':penerbit' => $data['penerbit'],
            ':tahun_terbit' => $data['tahun_terbit'],
            ':id' => $id
        ]);
    }

    public function delete(int $id): void {
        $stmt = $this->db->prepare('DELETE FROM buku WHERE id = :id');
        $stmt->execute([':id' => $id]);
    }
}
?>