<?php
class AuthMiddleware
{
    public function handle(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (empty($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
            $_SESSION['flash'] = [
                'type' => 'error',
                'message' => 'Silakan login terlebih dahulu.',
            ];

            $base = rtrim(str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'])), '/');
            header('Location: ' . ($base ? $base : '') . '/login');
            exit;
        }
    }
}
