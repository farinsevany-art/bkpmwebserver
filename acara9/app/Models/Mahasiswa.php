<?php

class Mahasiswa extends Model
{
    // Atribut objek dibuat private dan diakses melalui getter/setter.
    private string $nim = '';
    private string $nama = '';

    public function __construct(string $nim = '', string $nama = '')
    {
        parent::__construct();
        $this->setNim($nim);
        $this->setNama($nama);
    }

    public function getNim(): string
    {
        return $this->nim;
    }

    public function setNim(string $nim): void
    {
        $nim = trim($nim);
        if ($nim !== '' && !preg_match('/^(?=.*[A-Za-z])(?=(?:\D*\d){8,})[A-Za-z0-9]{9,20}$/', $nim)) {
            throw new InvalidArgumentException('NIM harus mengandung minimal 1 huruf dan 8 angka, hanya huruf/angka, dan panjang 9–20 karakter.');
        }
        $this->nim = $nim;
    }

    public function getNama(): string
    {
        return $this->nama;
    }

    public function setNama(string $nama): void
    {
        $nama = trim($nama);
        if (mb_strlen($nama) > 100) {
            throw new InvalidArgumentException('Nama maksimal 100 karakter.');
        }
        $this->nama = $nama;
    }

    private const SELECT = "
        SELECT
            m.*,
            p.kode_prodi AS prodi_kode,
            p.nama_prodi AS prodi_nama
        FROM mahasiswa m
        LEFT JOIN prodi p ON p.id_prodi = m.id_prodi
    ";

    // Format keyword pencarian
    private function like(string $keyword): string
    {
        return '%' . addcslashes($keyword, '%_\\') . '%';
    }

    // Menampilkan data mahasiswa dengan pencarian dan pagination
    public function all(
        string $keyword = '',
        int $limit = 10,
        int $offset = 0
    ): array {
        $sql = self::SELECT;
        $params = [];

        if ($keyword !== '') {
            $sql .= "
                WHERE m.nama LIKE :kw_nama
                   OR m.nim LIKE :kw_nim
            ";

            $params['kw_nama'] = $this->like($keyword);
            $params['kw_nim'] = $this->like($keyword);
        }

        $sql .= " ORDER BY m.nim ASC LIMIT :limit OFFSET :offset";

        $stmt = $this->db->prepare($sql);

        foreach ($params as $key => $value) {
            $stmt->bindValue(':' . $key, $value, PDO::PARAM_STR);
        }

        $stmt->bindValue(':limit', max(1, $limit), PDO::PARAM_INT);
        $stmt->bindValue(':offset', max(0, $offset), PDO::PARAM_INT);

        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Menghitung jumlah mahasiswa berdasarkan pencarian
    public function total(string $keyword = ''): int
    {
        if ($keyword === '') {
            return (int) $this->db
                ->query('SELECT COUNT(*) FROM mahasiswa')
                ->fetchColumn();
        }

        $stmt = $this->db->prepare("
            SELECT COUNT(*)
            FROM mahasiswa
            WHERE nama LIKE :kw_nama
               OR nim LIKE :kw_nim
        ");

        $stmt->execute([
            'kw_nama' => $this->like($keyword),
            'kw_nim' => $this->like($keyword),
        ]);

        return (int) $stmt->fetchColumn();
    }

    // Mencari mahasiswa berdasarkan NIM
    public function find(string $nim): ?array
    {
        $stmt = $this->db->prepare(
            self::SELECT . ' WHERE m.nim = :nim'
        );

        $stmt->execute(['nim' => $nim]);

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return $row ?: null;
    }

    // Memeriksa apakah NIM sudah digunakan
    public function nimExists(
        string $nim,
        ?string $exceptNim = null
    ): bool {
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

    // Memeriksa apakah email sudah digunakan
    public function emailExists(
        string $email,
        ?string $exceptNim = null
    ): bool {
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

    // Menambahkan mahasiswa baru
    public function create(array $d): bool
    {
        $stmt = $this->db->prepare("
            INSERT INTO mahasiswa (
                nim,
                nama,
                jenis_kelamin,
                alamat,
                email,
                id_prodi,
                angkatan,
                status
            ) VALUES (
                :nim,
                :nama,
                :jenis_kelamin,
                :alamat,
                :email,
                :id_prodi,
                :angkatan,
                :status
            )
        ");

        return $stmt->execute([
            'nim' => $d['nim'],
            'nama' => $d['nama'],
            'jenis_kelamin' => $d['jenis_kelamin'],
            'alamat' => $d['alamat'],
            'email' => $d['email'],
            'id_prodi' => $d['id_prodi'],
            'angkatan' => $d['angkatan'],
            'status' => $d['status'] ?? 'aktif',
        ]);
    }

    // Memperbarui mahasiswa berdasarkan NIM lama
    public function update(string $nimLama, array $d): bool
    {
        $stmt = $this->db->prepare("
            UPDATE mahasiswa
            SET
                nim = :nim,
                nama = :nama,
                jenis_kelamin = :jenis_kelamin,
                alamat = :alamat,
                email = :email,
                id_prodi = :id_prodi,
                angkatan = :angkatan,
                status = :status
            WHERE nim = :nim_lama
        ");

        return $stmt->execute([
            'nim' => $d['nim'],
            'nama' => $d['nama'],
            'jenis_kelamin' => $d['jenis_kelamin'],
            'alamat' => $d['alamat'],
            'email' => $d['email'],
            'id_prodi' => $d['id_prodi'],
            'angkatan' => $d['angkatan'],
            'status' => $d['status'] ?? 'aktif',
            'nim_lama' => $nimLama,
        ]);
    }

    // Menghapus mahasiswa berdasarkan NIM
    public function delete(string $nim): bool
    {
        $stmt = $this->db->prepare(
            'DELETE FROM mahasiswa WHERE nim = :nim'
        );

        return $stmt->execute(['nim' => $nim]);
    }
}
