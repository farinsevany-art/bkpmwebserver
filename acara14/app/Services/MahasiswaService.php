
<?php

/**
 * Service Layer untuk logika bisnis mahasiswa.
 * Validasi dan koordinasi proses create/update berada di sini.
 * Query database tetap ditangani oleh MahasiswaRepository.
 */
class MahasiswaService
{
    private MahasiswaRepository $mahasiswaRepository;
    private Prodi $prodi;

    public function __construct(
        MahasiswaRepository $mahasiswaRepository,
        Prodi $prodi
    ) {
        $this->mahasiswaRepository = $mahasiswaRepository;
        $this->prodi = $prodi;
    }

    /**
     * Menormalisasi data input sebelum validasi.
     */
    private function normalize(array $input): array
    {
        return [
            'nim' => trim((string) ($input['nim'] ?? '')),
            'nama' => trim((string) ($input['nama'] ?? '')),
            'jenis_kelamin' => (string) ($input['jenis_kelamin'] ?? 'L'),
            'alamat' => trim((string) ($input['alamat'] ?? '')),
            'email' => trim((string) ($input['email'] ?? '')),
            'id_prodi' => (int) ($input['id_prodi'] ?? 0),
            'angkatan' => (int) ($input['angkatan'] ?? 0),
            'status' => (string) ($input['status'] ?? 'aktif'),
        ];
    }

    /**
     * Memvalidasi data mahasiswa.
     * Mengembalikan data yang sudah dinormalisasi dan daftar error.
     */
    public function validate(array $input, ?string $nimLama = null): array
    {
        $data = $this->normalize($input);
        $errors = [];

        // Validasi NIM: huruf dan angka, minimal 8 dan maksimal 20 karakter.
        if ($data['nim'] === '') {
            $errors['nim'] = 'NIM wajib diisi.';
        } elseif (!preg_match('/^[A-Za-z0-9]{8,20}$/', $data['nim'])) {
            $errors['nim'] = 'NIM harus terdiri dari 8–20 karakter berupa huruf dan angka tanpa spasi.';
        } elseif ($this->mahasiswaRepository->nimExists($data['nim'], $nimLama)) {
            $errors['nim'] = 'NIM sudah terdaftar.';
        }

        // Validasi nama mahasiswa.
        if ($data['nama'] === '') {
            $errors['nama'] = 'Nama wajib diisi.';
        } elseif (mb_strlen($data['nama']) > 100) {
            $errors['nama'] = 'Nama maksimal 100 karakter.';
        }

        // Validasi email.
        if (
            $data['email'] === '' ||
            !filter_var($data['email'], FILTER_VALIDATE_EMAIL)
        ) {
            $errors['email'] = 'Format email tidak valid.';
        } elseif (strlen($data['email']) > 100) {
            $errors['email'] = 'Email maksimal 100 karakter.';
        } elseif (
            $this->mahasiswaRepository->emailExists(
                $data['email'],
                $nimLama
            )
        ) {
            $errors['email'] = 'Email sudah digunakan.';
        }

        // Validasi jenis kelamin.
        if (!in_array($data['jenis_kelamin'], ['L', 'P'], true)) {
            $errors['jenis_kelamin'] = 'Jenis kelamin tidak valid.';
        }

        // Validasi program studi.
        if (
            $data['id_prodi'] <= 0 ||
            !$this->prodi->find($data['id_prodi'])
        ) {
            $errors['id_prodi'] = 'Program studi wajib dipilih.';
        }

        // Validasi status mahasiswa.
        if (!in_array($data['status'], ['aktif', 'cuti', 'lulus'], true)) {
            $errors['status'] = 'Status mahasiswa tidak valid.';
        }

        // Validasi tahun angkatan.
        $maxYear = (int) date('Y') + 1;

        if (
            $data['angkatan'] < 2000 ||
            $data['angkatan'] > $maxYear
        ) {
            $errors['angkatan'] =
                "Angkatan harus antara 2000 dan {$maxYear}.";
        }

        // Validasi alamat.
        if (mb_strlen($data['alamat']) > 255) {
            $errors['alamat'] = 'Alamat maksimal 255 karakter.';
        }

        return [
            'data' => $data,
            'errors' => $errors,
        ];
    }

    /**
     * Menambahkan data mahasiswa.
     */
    public function create(array $input): array
    {
        try {
            $result = $this->validate($input);

            if ($result['errors'] !== []) {
                return [
                    'success' => false,
                    'data' => $result['data'],
                    'errors' => $result['errors'],
                ];
            }

            $success = $this->mahasiswaRepository->create(
                $result['data']
            );

            if (!$success) {
                throw new RuntimeException('Operasi tambah mahasiswa tidak berhasil.');
            }
        } catch (Throwable $e) {
            AppLogger::error($e, 'MahasiswaService::create');
            return [
                'success' => false,
                'data' => $this->normalize($input),
                'errors' => [
                    'general' =>
                        'Data mahasiswa gagal disimpan. Silakan coba lagi.',
                ],
            ];
        }

        return [
            'success' => true,
            'data' => $result['data'],
            'errors' => [],
        ];
    }

    /**
     * Memperbarui data mahasiswa.
     */
    public function update(string $nimLama, array $input): array
    {
        try {
            if (!$this->mahasiswaRepository->find($nimLama)) {
                return [
                    'success' => false,
                    'data' => $this->normalize($input),
                    'errors' => [
                        'general' => 'Data mahasiswa tidak ditemukan.',
                    ],
                ];
            }

            $result = $this->validate($input, $nimLama);

            if ($result['errors'] !== []) {
                return [
                    'success' => false,
                    'data' => $result['data'],
                    'errors' => $result['errors'],
                ];
            }

            $success = $this->mahasiswaRepository->update(
                $nimLama,
                $result['data']
            );

            if (!$success) {
                throw new RuntimeException('Operasi ubah mahasiswa tidak berhasil.');
            }
        } catch (Throwable $e) {
            AppLogger::error($e, 'MahasiswaService::update');
            return [
                'success' => false,
                'data' => $this->normalize($input),
                'errors' => [
                    'general' =>
                        'Data mahasiswa gagal diperbarui. Silakan coba lagi.',
                ],
            ];
        }

        return [
            'success' => true,
            'data' => $result['data'],
            'errors' => [],
        ];
    }
}
