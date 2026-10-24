<?php

class Prodi extends Model
{
    // Data program studi untuk dropdown
    public function options(): array
    {
        $stmt = $this->db->query("
            SELECT
                id_prodi,
                kode_prodi,
                nama_prodi,
                jenjang
            FROM prodi
            ORDER BY nama_prodi ASC
        ");

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Daftar program studi beserta jumlah mahasiswa dan mata kuliah
    public function all(): array
    {
        $sql = "
            SELECT
                p.*,
                (
                    SELECT COUNT(*)
                    FROM mahasiswa m
                    WHERE m.id_prodi = p.id_prodi
                ) AS jumlah_mahasiswa,
                (
                    SELECT COUNT(*)
                    FROM mata_kuliah mk
                    WHERE mk.id_prodi = p.id_prodi
                ) AS jumlah_mk
            FROM prodi p
            ORDER BY p.kode_prodi ASC
        ";

        return $this->db
            ->query($sql)
            ->fetchAll(PDO::FETCH_ASSOC);
    }

    // Menghitung jumlah program studi
    public function total(): int
    {
        return (int) $this->db
            ->query("SELECT COUNT(*) FROM prodi")
            ->fetchColumn();
    }

    // Mencari program studi berdasarkan ID
    public function find(int $id): ?array
    {
        $stmt = $this->db->prepare("
            SELECT *
            FROM prodi
            WHERE id_prodi = :id
        ");

        $stmt->execute(['id' => $id]);

        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }

    // Memeriksa apakah kode program studi sudah digunakan
    public function kodeExists(
        string $kode,
        ?int $exceptId = null
    ): bool {
        $sql = "
            SELECT COUNT(*)
            FROM prodi
            WHERE kode_prodi = :kode
        ";

        $params = ['kode' => $kode];

        if ($exceptId !== null) {
            $sql .= " AND id_prodi <> :id";
            $params['id'] = $exceptId;
        }

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);

        return (int) $stmt->fetchColumn() > 0;
    }

    // Menambahkan program studi
    public function create(array $d): int
    {
        $stmt = $this->db->prepare("
            INSERT INTO prodi (
                kode_prodi,
                nama_prodi,
                jenjang
            ) VALUES (
                :kode_prodi,
                :nama_prodi,
                :jenjang
            )
        ");

        $stmt->execute([
            'kode_prodi' => $d['kode_prodi'],
            'nama_prodi' => $d['nama_prodi'],
            'jenjang' => $d['jenjang'],
        ]);

        return (int) $this->db->lastInsertId();
    }

    // Memperbarui program studi
    public function update(int $id, array $d): bool
    {
        $stmt = $this->db->prepare("
            UPDATE prodi
            SET
                kode_prodi = :kode_prodi,
                nama_prodi = :nama_prodi,
                jenjang = :jenjang
            WHERE id_prodi = :id
        ");

        return $stmt->execute([
            'kode_prodi' => $d['kode_prodi'],
            'nama_prodi' => $d['nama_prodi'],
            'jenjang' => $d['jenjang'],
            'id' => $id,
        ]);
    }

    // Menghapus program studi
    public function delete(int $id): bool
    {
        $stmt = $this->db->prepare("
            DELETE FROM prodi
            WHERE id_prodi = :id
        ");

        return $stmt->execute(['id' => $id]);
    }
}
