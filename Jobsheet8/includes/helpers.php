<?php
// Helper bersama. Aman di-include berulang.

// Escape output HTML — cegah XSS. Bungkus setiap data dari database/user.
if (!function_exists('e')) {
    function e($nilai)
    {
        return htmlspecialchars((string) $nilai, ENT_QUOTES, 'UTF-8');
    }
}

// Flash message disimpan di cookie berumur pendek, BUKAN $_SESSION.
// Alasan: di hosting serverless (Vercel) tiap request bisa dilayani instance
// berbeda yang tidak berbagi file session, sehingga pesan bisa hilang.
if (!function_exists('set_flash')) {
    function set_flash($type, $pesan)
    {
        $https = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
            || (($_SERVER['HTTP_X_FORWARDED_PROTO'] ?? '') === 'https');
        setcookie('flash', json_encode(['type' => $type, 'pesan' => $pesan]), [
            'expires' => time() + 60,
            'path' => '/',
            'secure' => $https,
            'httponly' => true,
            'samesite' => 'Lax',
        ]);
    }
}

// Ambil lalu hapus flash. Harus dipanggil SEBELUM ada output HTML.
if (!function_exists('pull_flash')) {
    function pull_flash()
    {
        if (!isset($_COOKIE['flash'])) {
            return null;
        }
        $data = json_decode($_COOKIE['flash'], true);
        unset($_COOKIE['flash']);
        setcookie('flash', '', ['expires' => time() - 3600, 'path' => '/']);
        if (!is_array($data) || !isset($data['type'], $data['pesan'])) {
            return null;
        }
        $type = $data['type'] === 'success' ? 'success' : 'error';
        return ['type' => $type, 'pesan' => (string) $data['pesan']];
    }
}
