<?php
require __DIR__ . '/../includes/helpers.php';
require __DIR__ . '/../includes/koneksi.php';

$nama = trim($_POST['nama'] ?? '');
$nyawa = trim($_POST['nyawa'] ?? '');
$perlindungan = trim($_POST['perlindungan'] ?? '');

// Bilangan bulat 0 .. batas INTEGER PostgreSQL; false bila tidak valid.
$opsiAngka = ['options' => ['min_range' => 0, 'max_range' => 2147483647]];
$nyawaInt = filter_var($nyawa, FILTER_VALIDATE_INT, $opsiAngka);
$perlindunganInt = filter_var($perlindungan, FILTER_VALIDATE_INT, $opsiAngka);

$errors = [];
if ($nama === '') {
    $errors[] = "Nama wajib diisi.";
} elseif (strlen($nama) > 255) {
    $errors[] = "Nama maksimal 255 karakter.";
}
if ($nyawaInt === false) {
    $errors[] = "Nyawa harus berupa bilangan bulat tidak negatif.";
}
if ($perlindunganInt === false) {
    $errors[] = "Perlindungan harus berupa bilangan bulat tidak negatif.";
}

if (!empty($errors)) {
    set_flash('error', implode(' ', $errors));
    header('Location: tambah.php');
    exit;
}

try {
    $stmt = $pdo->prepare(
        "INSERT INTO karakter (nama, nyawa, perlindungan)
         VALUES (:nama, :nyawa, :perlindungan)
         RETURNING id"
    );
    $stmt->execute([
        'nama' => $nama,
        'nyawa' => $nyawaInt,
        'perlindungan' => $perlindunganInt,
    ]);
} catch (PDOException $e) {
    error_log('Gagal menyimpan karakter: ' . $e->getMessage());
    set_flash('error', 'Data gagal disimpan. Coba lagi.');
    header('Location: tambah.php');
    exit;
}

set_flash('success', 'Karakter berhasil ditambahkan.');
header('Location: list.php');
exit;
