<?php

/**
 * Membuat controller dan mengirim dependensi melalui constructor.
 * Ini contoh sederhana Constructor Dependency Injection.
 */
class ControllerFactory
{
    public static function make(string $controllerClass): object
    {
        switch ($controllerClass) {
            case MahasiswaController::class:
                return new MahasiswaController(new MahasiswaRepository(new Database()), new Prodi());

            case HomeController::class:
                return new HomeController(new MahasiswaRepository(new Database()), new Prodi(), new MataKuliah());

            default:
                // Controller lain belum membutuhkan dependensi constructor.
                return new $controllerClass();
        }
    }
}
