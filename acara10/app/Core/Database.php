<?php

/**
 * Mengelola koneksi database menggunakan PDO.
 * Mendukung dependency injection melalui objek Database.
 */
class Database
{
    private static ?PDO $connection = null;
    private PDO $pdo;

    public function __construct()
    {
        $this->pdo = self::connect();
    }

    /**
     * Mengambil koneksi PDO dari objek Database.
     */
    public function getConnection(): PDO
    {
        return $this->pdo;
    }

    /**
     * Kompatibilitas untuk model lama yang masih menggunakan Model.
     */
    public static function getInstance(): PDO
    {
        return self::connect();
    }

    private static function connect(): PDO
    {
        if (self::$connection === null) {
            $configFile = BASE_PATH . '/config/database.php';

            if (!is_file($configFile)) {
                throw new RuntimeException('File konfigurasi database tidak ditemukan.');
            }

            $config = require $configFile;
            $host = $config['host'] ?? '127.0.0.1';
            $port = $config['port'] ?? 3306;
            $dbname = $config['dbname'] ?? '';
            $charset = $config['charset'] ?? 'utf8mb4';
            $username = $config['username'] ?? 'root';
            $password = $config['password'] ?? '';

            if ($dbname === '') {
                throw new RuntimeException('Nama database belum diatur pada konfigurasi.');
            }

            $dsn = "mysql:host={$host};port={$port};dbname={$dbname};charset={$charset}";

            try {
                self::$connection = new PDO($dsn, $username, $password, [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES => false,
                ]);
            } catch (PDOException $e) {
                error_log('Koneksi database gagal: ' . $e->getMessage());
                throw new RuntimeException(
                    'Koneksi database gagal. Periksa MySQL dan konfigurasi database.',
                    0,
                    $e
                );
            }
        }

        return self::$connection;
    }

    private function __clone() {}
}
