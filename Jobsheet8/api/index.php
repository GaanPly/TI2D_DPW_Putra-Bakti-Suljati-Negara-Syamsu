<?php
// Front controller untuk Vercel (runtime vercel-php).
// Semua request diarahkan ke file ini lewat vercel.json, lalu diteruskan
// ke halaman yang sesuai. Hanya halaman & aset di whitelist yang bisa diakses,
// jadi includes/, sql/, dan README tidak pernah terbuka ke publik.
//
// Lokal (meniru Vercel):  php -S localhost:8000 api/index.php

$root = dirname(__DIR__);

$path = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH);
$path = rawurldecode(is_string($path) ? $path : '/');
if ($path === '' || $path === '/' || $path === '/api' || $path === '/api/index.php') {
    $path = '/index.php';
}
$path = rtrim($path, '/') === '/senjata' ? '/senjata/list.php' : $path;
$path = rtrim($path, '/') === '/karakter' ? '/karakter/list.php' : $path;

$halaman = [
    '/index.php',
    '/senjata/list.php',
    '/senjata/tambah.php',
    '/senjata/proses_tambah.php',
    '/karakter/list.php',
    '/karakter/tambah.php',
    '/karakter/proses_tambah.php',
];

if (in_array($path, $halaman, true)) {
    // Semua link/aset memakai path absolut dari root domain.
    define('APP_BASE_OVERRIDE', '/');
    require $root . $path; // sengaja di scope global agar variabel halaman terbagi
    return;
}

// Aset statis dilayani langsung oleh PHP (hanya assets/css/*.css dan assets/js/*.js).
if (preg_match('#^/assets/(css|js)/[A-Za-z0-9._-]+\.(css|js)$#', $path, $m)) {
    $file = $root . $path;
    if (is_file($file)) {
        header('Content-Type: ' . ($m[2] === 'css' ? 'text/css' : 'application/javascript') . '; charset=UTF-8');
        header('Cache-Control: public, max-age=3600');
        readfile($file);
        return;
    }
}

http_response_code(404);
header('Content-Type: text/plain; charset=UTF-8');
echo '404 - Halaman tidak ditemukan';
