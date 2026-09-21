<?php
require_once __DIR__ . '/helpers.php';

// Flash message (dari cookie) — diambil sebelum ada output HTML.
$flash = pull_flash();

// Prefix relatif ke root proyek ini (bukan root domain) — supaya
// /assets, /index.php, dst tetap benar walau proyek diakses lewat
// subfolder (mis. dp2026.test/kode-praktikum/jobsheet-08/), bukan cuma
// lewat vhost yang document root-nya langsung folder ini.
// Di Vercel, api/index.php mendefinisikan APP_BASE_OVERRIDE = '/'.
$__jobsheetRoot = dirname(__DIR__);
$__scriptDir = dirname($_SERVER['SCRIPT_FILENAME']);
$__rel = ltrim(str_replace('\\', '/', substr($__scriptDir, strlen($__jobsheetRoot))), '/');
$base = $__rel === '' ? '' : str_repeat('../', substr_count($__rel, '/') + 1);
if (defined('APP_BASE_OVERRIDE')) {
    $base = APP_BASE_OVERRIDE;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Game Database<?php echo isset($page_title) ? ' | ' . $page_title : ''; ?></title>
    <link rel="stylesheet" href="<?php echo $base; ?>assets/css/style.css">
</head>
<body>
    <header>
        <h1>Game Database</h1>
        <button type="button" id="nav-toggle-btn" class="nav-toggle-label" aria-label="Menu">&#9776;</button>
        <nav>
            <ul>
                <li><a href="<?php echo $base; ?>index.php">Beranda</a></li>
                <li><a href="<?php echo $base; ?>senjata/list.php">Daftar Senjata</a></li>
                <li><a href="<?php echo $base; ?>senjata/tambah.php">Tambah Senjata</a></li>
                <li><a href="<?php echo $base; ?>karakter/list.php">Daftar Karakter</a></li>
                <li><a href="<?php echo $base; ?>karakter/tambah.php">Tambah Karakter</a></li>
            </ul>
        </nav>
    </header>

    <main>
