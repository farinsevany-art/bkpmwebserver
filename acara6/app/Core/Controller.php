<?php
class Controller
{
    protected function view(string $view, array $data = []): void
    {
        extract($data);
        require BASE_PATH . '/app/Views/' . $view . '.php';
    }

    protected function redirect(string $path): never
    {
        header('Location: ' . $this->url($path));
        exit;
    }

    protected function url(string $path = ''): string
    {
        $base = rtrim(str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'])), '/');
        return ($base ? $base : '') . '/' . ltrim($path, '/');
    }

    protected function e(string $value): string
    {
        return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
    }
}
