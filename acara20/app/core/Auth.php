<?php

class Auth {
    // Secret key untuk signature JWT
    const SECRET = 'kunci_rahasia_workshop_2026';

    private static function base64url(string $data): string {
        return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
    }

    private static function base64urlDecode(string $data) {
        $base64 = strtr($data, '-_', '+/');
        $padding = strlen($base64) % 4;
        if ($padding !== 0) {
            $base64 .= str_repeat('=', 4 - $padding);
        }

        return base64_decode($base64, true);
    }

    /**
     * Membuat token JWT dengan payload pengguna.
     * Masa berlaku token adalah 20 menit (1.200 detik).
     */
    public static function generateToken(array $payload): string {
        $header = self::base64url(json_encode(['alg' => 'HS256', 'typ' => 'JWT']));
        // Expired time: 20 menit (1.200 detik) dari waktu sekarang
        $payload['exp'] = time() + 1200;
        $payloadEnc = self::base64url(json_encode($payload));
        $signature = self::base64url(hash_hmac('sha256', "$header.$payloadEnc", self::SECRET, true));
        return "$header.$payloadEnc.$signature";
    }

    public static function validateToken(string $token) {
        $parts = explode('.', $token);
        if (count($parts) !== 3 || in_array('', $parts, true)) return false;
        [$header, $payload, $signature] = $parts;

        $headerData = self::base64urlDecode($header);
        if ($headerData === false) return false;
        $headerData = json_decode($headerData, true);
        if (!is_array($headerData) || ($headerData['alg'] ?? null) !== 'HS256') return false;

        $valid = self::base64url(hash_hmac('sha256', "$header.$payload", self::SECRET, true));
        if (!hash_equals($valid, $signature)) return false;

        $payloadData = self::base64urlDecode($payload);
        if ($payloadData === false) return false;
        $data = json_decode($payloadData, true);
        if (!is_array($data) || !isset($data['exp']) || !is_numeric($data['exp']) || $data['exp'] <= time()) {
            return false;
        }

        return $data;
    }

    public static function requireAuth(): array {
        $headers = function_exists('getallheaders') ? getallheaders() : [];
        if (!is_array($headers)) {
            $headers = [];
        }

        $authHeader = $_SERVER['HTTP_AUTHORIZATION']
            ?? $_SERVER['REDIRECT_HTTP_AUTHORIZATION']
            ?? '';
        foreach ($headers as $name => $value) {
            if (strcasecmp((string) $name, 'Authorization') === 0) {
                $authHeader = $value;
                break;
            }
        }

        if (!is_string($authHeader) || !preg_match('/^Bearer\s+(\S+)$/i', $authHeader, $m)) {
            Response::json(401, false, 'Token tidak ditemukan');
        }
        $payload = self::validateToken($m[1]);
        if (!$payload) {
            Response::json(401, false, 'Token tidak valid atau kedaluwarsa');
        }
        return $payload;
    }
}
