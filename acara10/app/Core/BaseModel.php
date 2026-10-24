<?php

/**
 * BaseModel menyediakan akses PDO yang dapat digunakan oleh model turunan.
 */
abstract class BaseModel
{
    protected PDO $db;

    public function __construct(?Database $database = null)
    {
        $database ??= new Database();
        $this->db = $database->getConnection();
    }
}
