<?php
class Database {
    private $host = "localhost";
    private $db_name = "si-akademik";
    private $username = "root";
    private $password = "";
    public $conn;

    public function getConnection() {
        $dsn = "mysql:host={$this->host};dbname={$this->db_name};charset=utf8mb4";
        $this->conn = new PDO($dsn, $this->username, $this->password, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false
        ]);

        return $this->conn;
    }
}