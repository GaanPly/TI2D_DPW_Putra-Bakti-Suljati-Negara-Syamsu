<?php
// Konfigurasi koneksi PostgreSQL.
// Prioritas: DATABASE_URL (Render/Railway/Neon/Supabase) -> variabel DB_* -> default lokal.
// Tanpa konfigurasi apa pun, aplikasi memakai default lokal di bawah ini.
$host    = getenv('DB_HOST') ?: 'localhost';
$port    = getenv('DB_PORT') ?: '5432';
$db      = getenv('DB_NAME') ?: 'game_database';
$user    = getenv('DB_USER') ?: 'postgres';
$pass    = getenv('DB_PASS') !== false ? getenv('DB_PASS') : 'postgres';
$sslmode = getenv('DB_SSLMODE') ?: '';

$url = getenv('DATABASE_URL');
if ($url) {
    $p = parse_url($url);
    if ($p !== false && isset($p['host'])) {
        $host = $p['host'];
        $port = $p['port'] ?? $port;
        $db   = isset($p['path']) ? ltrim($p['path'], '/') : $db;
        $user = isset($p['user']) ? urldecode($p['user']) : $user;
        $pass = isset($p['pass']) ? urldecode($p['pass']) : $pass;
        if (!empty($p['query'])) {
            parse_str($p['query'], $q);
            if (!empty($q['sslmode'])) {
                $sslmode = $q['sslmode'];
            }
        }
    }
}

$dsn = "pgsql:host=$host;port=$port;dbname=$db" . ($sslmode !== '' ? ";sslmode=$sslmode" : '');

try {
    $pdo = new PDO($dsn, $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    error_log('Koneksi database gagal: ' . $e->getMessage());
    http_response_code(500);
    // Detail error hanya ditampilkan di luar mode production.
    $detail = getenv('APP_ENV') === 'production' ? '' : ': ' . htmlspecialchars($e->getMessage(), ENT_QUOTES, 'UTF-8');
    die('Koneksi database gagal' . $detail);
}

// Buat tabel otomatis bila belum ada (aman dijalankan berulang: skema memakai
// CREATE TABLE IF NOT EXISTS). Matikan dengan environment variable AUTO_MIGRATE=0.
if (getenv('AUTO_MIGRATE') !== '0') {
    $jumlahTabel = (int) $pdo->query(
        "SELECT COUNT(*) FROM information_schema.tables
         WHERE table_schema = 'public' AND table_name IN ('senjata', 'karakter')"
    )->fetchColumn();
    if ($jumlahTabel < 2) {
        $pdo->exec(file_get_contents(__DIR__ . '/../sql/database_kartu.sql'));
    }
}
