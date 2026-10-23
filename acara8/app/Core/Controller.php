<?php

class Controller
{
    /**
     * Menampilkan view dengan data yang dikirim controller.
     */
    protected function view(string $view, array $data = []): void
    {
        $file = BASE_PATH . '/app/Views/' . $view . '.php';

        if (!is_file($file)) {
            $this->notFound();
            return;
        }

        extract($data, EXTR_SKIP);

        require $file;
    }

    /**
     * Redirect ke halaman tertentu.
     */
    protected function redirect(string $path): never
    {
        header('Location: ' . $this->url($path));
        exit;
    }

    /**
     * Membuat URL aplikasi.
     */
    public function url(string $path = ''): string
    {
        $scriptName = str_replace(
            '\\',
            '/',
            $_SERVER['SCRIPT_NAME'] ?? ''
        );

        $base = rtrim(dirname($scriptName), '/');

        if ($base === '/' || $base === '.') {
            $base = '';
        }

        return $base . '/' . ltrim($path, '/');
    }

    /**
     * Mengamankan teks sebelum ditampilkan di HTML.
     */
    public function e(mixed $value): string
    {
        return htmlspecialchars(
            (string) $value,
            ENT_QUOTES | ENT_SUBSTITUTE,
            'UTF-8'
        );
    }

    /**
     * Menyimpan pesan notifikasi sementara.
     */
    protected function flash(string $type, string $message): void
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }

        $_SESSION['flash'] = [
            'type' => $type,
            'message' => $message,
        ];
    }

    /**
     * Mengambil pesan notifikasi dan menghapusnya dari session.
     */
    protected function getFlash(): ?array
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }

        $flash = $_SESSION['flash'] ?? null;

        unset($_SESSION['flash']);

        return is_array($flash) ? $flash : null;
    }

    /**
     * Menampilkan halaman 404.
     */
    protected function notFound(): void
    {
        http_response_code(404);

        $file = BASE_PATH . '/app/Views/errors/404.php';

        if (is_file($file)) {
            require $file;
            return;
        }

        echo '<!DOCTYPE html>';
        echo '<html lang="id">';
        echo '<head>';
        echo '<meta charset="UTF-8">';
        echo '<meta name="viewport" content="width=device-width, initial-scale=1.0">';
        echo '<title>404 - Halaman Tidak Ditemukan</title>';
        echo '</head>';
        echo '<body>';
        echo '<h1>404 - Halaman Tidak Ditemukan</h1>';
        echo '<p>Halaman yang kamu cari tidak tersedia.</p>';
        echo '<a href="' . $this->e($this->url('mahasiswa')) . '">';
        echo 'Kembali ke Data Mahasiswa';
        echo '</a>';
        echo '</body>';
        echo '</html>';
    }
}
