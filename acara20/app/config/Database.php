<?php

define('APP_ENV', 'development');

class Database {
    private static $host = 'localhost';
    private static $user = 'root';
    private static $pass = '';
    private static $db_name = 'si-akademik';
    private static $conn = null;

    public static function getConnection() {
        if (self::$conn === null) {
            $dsn = 'mysql:host=' . self::$host . ';dbname=' . self::$db_name;
            $options = [
                PDO::ATTR_PERSISTENT => true,
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            ];

            try {
                self::$conn = new PDO($dsn, self::$user, self::$pass, $options);
            } catch (PDOException $e) {
                die('Koneksi Database Gagal: ' . $e->getMessage());
            }
        }

        return self::$conn;
    }
}
