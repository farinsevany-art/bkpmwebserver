<?php

if (!defined('APP_ENV')) {
    $appEnvironment = getenv('APP_ENV');
    define('APP_ENV', $appEnvironment !== false && $appEnvironment !== '' ? $appEnvironment : 'development');
}
if (APP_ENV !== 'production' && !defined('APP_LOCAL_JWT_SECRET')) {
    define('APP_LOCAL_JWT_SECRET', 'acara28-local-development-secret-not-for-production');
}

class Database {
    private static $conn = null;

    public static function getConnection() {
        if (self::$conn === null) {
            $production = APP_ENV === 'production';
            $host = getenv('DB_HOST');
            $dbName = getenv('DB_NAME');
            $user = getenv('DB_USER');
            $password = getenv('DB_PASS');

            if ($production && (
                $host === false || $host === ''
                || $dbName === false || $dbName === ''
                || $user === false || $user === ''
                || $password === false || $password === ''
            )) {
                error_log('Production database environment variables are incomplete');
                Response::json(500, false, 'Konfigurasi database server belum lengkap');
            }

            $host = $host === false || $host === '' ? 'localhost' : $host;
            $dbName = $dbName === false || $dbName === '' ? 'si-akademik' : $dbName;
            $user = $user === false || $user === '' ? 'root' : $user;
            $password = $password === false ? '' : $password;
            $dsn = 'mysql:host=' . $host . ';dbname=' . $dbName . ';charset=utf8mb4';
            $options = [
                PDO::ATTR_PERSISTENT => true,
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
            ];

            try {
                self::$conn = new PDO($dsn, $user, $password, $options);
            } catch (PDOException $e) {
                error_log('Database connection failed: ' . $e->getMessage());
                Response::json(500, false, 'Koneksi database gagal');
            }
        }
        return self::$conn;
    }
}
