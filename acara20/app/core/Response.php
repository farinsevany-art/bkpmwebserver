<?php

class Response {
    public static function json(int $httpCode, bool $success, string $message, $data = null, array $errors = []): void {
        http_response_code($httpCode);
        header('Content-Type: application/json');

        $jsonFlags = JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE;
        if (defined('APP_ENV') && APP_ENV === 'development') {
            $jsonFlags |= JSON_PRETTY_PRINT;
        }

        $response = [
            'success' => $success,
            'message' => $message,
            'data' => $data,
            'errors' => $errors,
            'timestamp' => date('c'),
        ];

        echo json_encode($response, $jsonFlags);
        exit;
    }
}
