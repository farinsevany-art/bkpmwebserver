<?php

/**
 * Koneksi database menggunakan pola Singleton.
 * Satu koneksi PDO digunakan bersama oleh seluruh aplikasi.
 */
class Database
{
    private static ?PDO $instance = null;

    /**
     * Mengambil koneksi database.
     */
    public static function getInstance(): PDO
    {
        if (self::$instance === null) {
            $configFile = BASE_PATH . '/config/database.php';

            if (!is_file($configFile)) {
                http_response_code(500);
                exit('File konfigurasi database tidak ditemukan.');
            }

            $config = require $configFile;

            $host = $config['host'] ?? '127.0.0.1';
            $port = $config['port'] ?? 3306;
            $dbname = $config['dbname'] ?? '';
            $charset = $config['charset'] ?? 'utf8mb4';
            $username = $config['username'] ?? 'root';
            $password = $config['password'] ?? '';

            if ($dbname === '') {
                http_response_code(500);
                exit('Nama database belum diatur pada konfigurasi.');
            }

            $dsn = "mysql:host={$host};port={$port};dbname={$dbname};charset={$charset}";

            try {
                self::$instance = new PDO(
                    $dsn,
                    $username,
                    $password,
                    [
                        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                        PDO::ATTR_EMULATE_PREPARES => false,
                    ]
                );
            } catch (PDOException $e) {
                http_response_code(500);

                // Detail error dicatat di log server, bukan ditampilkan
                // kepada pengguna.
                error_log('Koneksi database gagal: ' . $e->getMessage());

                exit(
                    '<h3>Koneksi database gagal.</h3>' .
                    '<p>Pastikan MySQL di XAMPP sudah berjalan, ' .
                    'nama database benar, dan konfigurasi database sesuai.</p>'
                );
            }
        }

        return self::$instance;
    }

    /**
     * Mencegah pembuatan objek secara langsung.
     */
    private function __construct()
    {
    }

    /**
     * Mencegah cloning objek.
     */
    private function __clone()
    {
    }

    /**
     * Mencegah unserialize objek.
     */
    public function __wakeup(): void
    {
        throw new LogicException(
            'Objek Database tidak dapat dibuat melalui unserialize.'
        );
    }
}
