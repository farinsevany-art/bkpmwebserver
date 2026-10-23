<?php
class AuthController extends Controller
{
    public function loginForm(): void
    {
        if (!empty($_SESSION['logged_in'])) {
            $this->redirect('dashboard');
        }

        $flash = $_SESSION['flash'] ?? null;
        unset($_SESSION['flash']);
        $this->view('auth/login', ['flash' => $flash]);
    }

    public function login(): void
    {
        $username = trim($_POST['username'] ?? '');
        $password = $_POST['password'] ?? '';

    
        if ($username === 'admin' && $password === 'admin123') {
            session_regenerate_id(true);
            $_SESSION['user_id'] = 1;
            $_SESSION['user_name'] = 'Admin';
            $_SESSION['logged_in'] = true;
            $_SESSION['flash'] = [
                'type' => 'success',
                'message' => 'Selamat datang, Admin!',
            ];
            $this->redirect('dashboard');
        }

        $_SESSION['flash'] = [
            'type' => 'error',
            'message' => 'Username atau password salah.',
        ];
        $this->redirect('login');
    }

    public function logout(): void
    {
        $_SESSION = [];
        if (ini_get('session.use_cookies')) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000, $params['path'], $params['domain'], $params['secure'], $params['httponly']);
        }
        session_destroy();
        session_start();
        $_SESSION['flash'] = [
            'type' => 'success',
            'message' => 'Anda telah logout.',
        ];
        $this->redirect('login');
    }
}
