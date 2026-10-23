
<?php

class AuthController extends Controller
{
    public function loginForm(): void
    {
        if (!empty($_SESSION['logged_in'])) {
            $this->redirect('dashboard');
        }

        $this->view('auth/login', [
            'title' => 'Login Administrator',
        ]);
    }

    /**
     * Memproses login administrator.
     */
    public function login(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('login');
        }

        $username = trim($_POST['username'] ?? '');
        $password = $_POST['password'] ?? '';

        // Kredensial demo.
        // Untuk aplikasi produksi, gunakan database dan password_hash().
        $validUsername = 'admin';
        $validPassword = 'admin123';

        if (
            $username === $validUsername &&
            $password === $validPassword
        ) {
            session_regenerate_id(true);

            $_SESSION['user_id'] = 1;
            $_SESSION['user_name'] = 'Admin';
            $_SESSION['logged_in'] = true;

            $this->flash('success', 'Selamat datang, Admin!');
            $this->redirect('dashboard');
        }

        $this->flash('danger', 'Username atau password salah.');
        $this->redirect('login');
    }

    /**
     * Mengakhiri sesi login.
     */
    public function logout(): void
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }

        $_SESSION = [];

        // Menghapus cookie session jika digunakan.
        if (ini_get('session.use_cookies')) {
            $params = session_get_cookie_params();

            setcookie(
                session_name(),
                '',
                [
                    'expires' => time() - 42000,
                    'path' => $params['path'],
                    'domain' => $params['domain'],
                    'secure' => $params['secure'],
                    'httponly' => $params['httponly'],
                    'samesite' => $params['samesite'] ?? 'Lax',
                ]
            );
        }

        session_destroy();

        // Memulai sesi baru agar pesan logout dapat disimpan.
        session_start();

        $this->flash('success', 'Anda telah logout.');
        $this->redirect('login');
    }
}
