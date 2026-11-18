<?php

class Auth {
    private const SECRET = 'kunci_rahasia_workshop_2026';

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

    public static function generateToken(array $payload): string {
        $header = self::base64url(json_encode(['alg' => 'HS256', 'typ' => 'JWT']));
        $payload['exp'] = time() + 300;
        $payloadEncoded = self::base64url(json_encode($payload));
        $signature = self::base64url(
            hash_hmac('sha256', "$header.$payloadEncoded", self::SECRET, true)
        );

        return "$header.$payloadEncoded.$signature";
    }

    public static function validateToken(string $token) {
        $parts = explode('.', $token);
        if (count($parts) !== 3 || in_array('', $parts, true)) {
            return false;
        }

        [$header, $payload, $signature] = $parts;
        $headerJson = self::base64urlDecode($header);
        if ($headerJson === false) {
            return false;
        }

        $headerData = json_decode($headerJson, true);
        if (!is_array($headerData) || ($headerData['alg'] ?? null) !== 'HS256') {
            return false;
        }

        $expectedSignature = self::base64url(
            hash_hmac('sha256', "$header.$payload", self::SECRET, true)
        );
        if (!hash_equals($expectedSignature, $signature)) {
            return false;
        }

        $payloadJson = self::base64urlDecode($payload);
        if ($payloadJson === false) {
            return false;
        }

        $payloadData = json_decode($payloadJson, true);
        if (
            !is_array($payloadData)
            || !isset($payloadData['exp'])
            || !is_numeric($payloadData['exp'])
            || $payloadData['exp'] <= time()
        ) {
            return false;
        }

        return $payloadData;
    }

    public static function requireAuth(): array {
        $headers = function_exists('getallheaders') ? getallheaders() : [];
        if (!is_array($headers)) {
            $headers = [];
        }

        $authorization = $_SERVER['HTTP_AUTHORIZATION']
            ?? $_SERVER['REDIRECT_HTTP_AUTHORIZATION']
            ?? '';
        foreach ($headers as $name => $value) {
            if (strcasecmp((string) $name, 'Authorization') === 0) {
                $authorization = $value;
                break;
            }
        }

        if (!is_string($authorization) || !preg_match('/^Bearer\s+(\S+)$/i', $authorization, $matches)) {
            Response::json(401, false, 'Token tidak ditemukan');
        }

        $payload = self::validateToken($matches[1]);
        if ($payload === false) {
            Response::json(401, false, 'Token tidak valid atau kedaluwarsa');
        }

        return $payload;
    }
}
