<?php

/**
 * Repository untuk seluruh operasi database mahasiswa.
 * Koneksi PDO diterima melalui constructor (Dependency Injection).
 */
class MahasiswaRepository
{
    private PDO $db;

    public function __construct(Database $database)
    {
        $this->db = $database->getConnection();
    }

    private const SELECT = "
        SELECT
            m.*,
            p.kode_prodi AS prodi_kode,
            p.nama_prodi AS prodi_nama
        FROM mahasiswa m
        LEFT JOIN prodi p ON p.id_prodi = m.id_prodi
    ";

    private function like(string $keyword): string
    {
        return '%' . addcslashes($keyword, '%_\\\\') . '%';
    }

    public function all(string $keyword = '', int $limit = 10, int $offset = 0): array
    {
        $sql = self::SELECT;
        $params = [];

        if ($keyword !== '') {
            $sql .= ' WHERE m.nama LIKE :kw_nama OR m.nim LIKE :kw_nim';
            $params = [
                'kw_nama' => $this->like($keyword),
                'kw_nim' => $this->like($keyword),
            ];
        }

        $sql .= ' ORDER BY m.nim ASC LIMIT :limit OFFSET :offset';
        $stmt = $this->db->prepare($sql);

        foreach ($params as $key => $value) {
            $stmt->bindValue(':' . $key, $value, PDO::PARAM_STR);
        }

        $stmt->bindValue(':limit', max(1, $limit), PDO::PARAM_INT);
        $stmt->bindValue(':offset', max(0, $offset), PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function total(string $keyword = ''): int
    {
        if ($keyword === '') {
            return (int) $this->db->query('SELECT COUNT(*) FROM mahasiswa')->fetchColumn();
        }

        $stmt = $this->db->prepare(
            'SELECT COUNT(*) FROM mahasiswa WHERE nama LIKE :kw_nama OR nim LIKE :kw_nim'
        );
        $stmt->execute([
            'kw_nama' => $this->like($keyword),
            'kw_nim' => $this->like($keyword),
        ]);

        return (int) $stmt->fetchColumn();
    }

    public function find(string $nim): ?array
    {
        $stmt = $this->db->prepare(self::SELECT . ' WHERE m.nim = :nim');
        $stmt->execute(['nim' => $nim]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return $row ?: null;
    }

    public function nimExists(string $nim, ?string $exceptNim = null): bool
    {
        $sql = 'SELECT COUNT(*) FROM mahasiswa WHERE nim = :nim';
        $params = ['nim' => $nim];

        if ($exceptNim !== null) {
            $sql .= ' AND nim <> :except_nim';
            $params['except_nim'] = $exceptNim;
        }

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);

        return (int) $stmt->fetchColumn() > 0;
    }

    public function emailExists(string $email, ?string $exceptNim = null): bool
    {
        $sql = 'SELECT COUNT(*) FROM mahasiswa WHERE email = :email';
        $params = ['email' => $email];

        if ($exceptNim !== null) {
            $sql .= ' AND nim <> :except_nim';
            $params['except_nim'] = $exceptNim;
        }

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);

        return (int) $stmt->fetchColumn() > 0;
    }

    public function create(array $data): bool
    {
        $stmt = $this->db->prepare("
            INSERT INTO mahasiswa
                (nim, nama, jenis_kelamin, alamat, email, id_prodi, angkatan, status)
            VALUES
                (:nim, :nama, :jenis_kelamin, :alamat, :email, :id_prodi, :angkatan, :status)
        ");

        return $stmt->execute([
            'nim' => $data['nim'],
            'nama' => $data['nama'],
            'jenis_kelamin' => $data['jenis_kelamin'],
            'alamat' => $data['alamat'],
            'email' => $data['email'],
            'id_prodi' => $data['id_prodi'],
            'angkatan' => $data['angkatan'],
            'status' => $data['status'] ?? 'aktif',
        ]);
    }

    public function update(string $nimLama, array $data): bool
    {
        $stmt = $this->db->prepare("
            UPDATE mahasiswa
            SET nim = :nim, nama = :nama, jenis_kelamin = :jenis_kelamin,
                alamat = :alamat, email = :email, id_prodi = :id_prodi,
                angkatan = :angkatan, status = :status
            WHERE nim = :nim_lama
        ");

        return $stmt->execute([
            'nim' => $data['nim'],
            'nama' => $data['nama'],
            'jenis_kelamin' => $data['jenis_kelamin'],
            'alamat' => $data['alamat'],
            'email' => $data['email'],
            'id_prodi' => $data['id_prodi'],
            'angkatan' => $data['angkatan'],
            'status' => $data['status'] ?? 'aktif',
            'nim_lama' => $nimLama,
        ]);
    }

    public function delete(string $nim): bool
    {
        $stmt = $this->db->prepare('DELETE FROM mahasiswa WHERE nim = :nim');
        return $stmt->execute(['nim' => $nim]);
    }
}
