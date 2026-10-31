<?php

class MataKuliah extends Model
{
    private const SELECT = "
        SELECT
            mk.*,
            p.kode_prodi AS prodi_kode,
            p.nama_prodi AS prodi_nama
        FROM mata_kuliah mk
        LEFT JOIN prodi p ON p.id_prodi = mk.id_prodi
    ";

    // Menampilkan semua data mata kuliah
    public function all(): array
    {
        return $this->db
            ->query(
                self::SELECT . "
                    ORDER BY mk.semester ASC, mk.kode_mk ASC
                "
            )
            ->fetchAll(PDO::FETCH_ASSOC);
    }

    // Menghitung jumlah mata kuliah
    public function total(): int
    {
        return (int) $this->db
            ->query("SELECT COUNT(*) FROM mata_kuliah")
            ->fetchColumn();
    }

    // Mencari mata kuliah berdasarkan ID
    public function find(int $id): ?array
    {
        $stmt = $this->db->prepare(
            self::SELECT . " WHERE mk.id_mk = :id"
        );

        $stmt->execute(['id' => $id]);

        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }

    // Memeriksa apakah kode mata kuliah sudah digunakan
    public function kodeExists(
        string $kode,
        ?int $exceptId = null
    ): bool {
        $sql = "
            SELECT COUNT(*)
            FROM mata_kuliah
            WHERE kode_mk = :kode
        ";

        $params = ['kode' => $kode];

        if ($exceptId !== null) {
            $sql .= " AND id_mk <> :id";
            $params['id'] = $exceptId;
        }

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);

        return (int) $stmt->fetchColumn() > 0;
    }

    // Menambahkan mata kuliah baru
    public function create(array $d): int
    {
        $stmt = $this->db->prepare("
            INSERT INTO mata_kuliah (
                kode_mk,
                nama_mk,
                sks,
                semester,
                id_prodi
            ) VALUES (
                :kode_mk,
                :nama_mk,
                :sks,
                :semester,
                :id_prodi
            )
        ");

        $stmt->execute([
            'kode_mk' => $d['kode_mk'],
            'nama_mk' => $d['nama_mk'],
            'sks' => $d['sks'],
            'semester' => $d['semester'],
            'id_prodi' => $d['id_prodi'],
        ]);

        return (int) $this->db->lastInsertId();
    }

    // Memperbarui data mata kuliah
    public function update(int $id, array $d): bool
    {
        $stmt = $this->db->prepare("
            UPDATE mata_kuliah
            SET
                kode_mk = :kode_mk,
                nama_mk = :nama_mk,
                sks = :sks,
                semester = :semester,
                id_prodi = :id_prodi
            WHERE id_mk = :id
        ");

        return $stmt->execute([
            'kode_mk' => $d['kode_mk'],
            'nama_mk' => $d['nama_mk'],
            'sks' => $d['sks'],
            'semester' => $d['semester'],
            'id_prodi' => $d['id_prodi'],
            'id' => $id,
        ]);
    }

    // Menghapus mata kuliah berdasarkan ID
    public function delete(int $id): bool
    {
        $stmt = $this->db->prepare(
            "DELETE FROM mata_kuliah WHERE id_mk = :id"
        );

        return $stmt->execute(['id' => $id]);
    }
}
