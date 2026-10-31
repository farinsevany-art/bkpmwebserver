<?php
/** Pencatatan error aplikasi tanpa membocorkan detail teknis kepada pengguna. */
class AppLogger
{
    public static function error(Throwable $exception, string $context = 'Aplikasi'): void
    {
        $directory = BASE_PATH . '/storage/logs';
        if (!is_dir($directory)) {
            @mkdir($directory, 0775, true);
        }
        $message = sprintf(
            "[%s] %s | %s: %s | %s:%d%s",
            date('Y-m-d H:i:s'),
            $context,
            get_class($exception),
            $exception->getMessage(),
            $exception->getFile(),
            $exception->getLine(),
            PHP_EOL
        );
        error_log($message, 3, $directory . '/app.log');
    }
}
