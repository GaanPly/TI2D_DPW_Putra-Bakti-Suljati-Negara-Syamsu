<?php
require __DIR__ . '/../includes/helpers.php';
require __DIR__ . '/../includes/koneksi.php';

$nama = trim($_POST['nama'] ?? '');
$deskripsi = trim($_POST['deskripsi'] ?? '');
$kerusakan = trim($_POST['kerusakan'] ?? '');
$durabilitas = trim($_POST['durabilitas'] ?? '');

// Bilangan bulat 0 .. batas INTEGER PostgreSQL; false bila tidak valid.
$opsiAngka = ['options' => ['min_range' => 0, 'max_range' => 2147483647]];
$kerusakanInt = filter_var($kerusakan, FILTER_VALIDATE_INT, $opsiAngka);
$durabilitasInt = filter_var($durabilitas, FILTER_VALIDATE_INT, $opsiAngka);

// Validasi server-side — wajib ada meski sudah divalidasi JS di Jobsheet 5,
// karena validasi client bisa dilewati (nonaktifkan JS / kirim request manual).
$errors = [];
if ($nama === '') {
    $errors[] = "Nama wajib diisi.";
} elseif (strlen($nama) > 255) {
    $errors[] = "Nama maksimal 255 karakter.";
}
if ($deskripsi === '') {
    $errors[] = "Deskripsi wajib diisi.";
} elseif (strlen($deskripsi) > 255) {
    $errors[] = "Deskripsi maksimal 255 karakter.";
}
if ($kerusakanInt === false) {
    $errors[] = "Kerusakan harus berupa bilangan bulat tidak negatif.";
}
if ($durabilitasInt === false) {
    $errors[] = "Durabilitas harus berupa bilangan bulat tidak negatif.";
}

if (!empty($errors)) {
    set_flash('error', implode(' ', $errors));
    header('Location: tambah.php');
    exit;
}

try {
    $stmt = $pdo->prepare(
        "INSERT INTO senjata (nama, deskripsi, kerusakan, durabilitas)
         VALUES (:nama, :deskripsi, :kerusakan, :durabilitas)
         RETURNING id"
    );
    $stmt->execute([
        'nama' => $nama,
        'deskripsi' => $deskripsi,
        'kerusakan' => $kerusakanInt,
        'durabilitas' => $durabilitasInt,
    ]);
} catch (PDOException $e) {
    error_log('Gagal menyimpan senjata: ' . $e->getMessage());
    set_flash('error', 'Data gagal disimpan. Coba lagi.');
    header('Location: tambah.php');
    exit;
}

set_flash('success', 'Senjata berhasil ditambahkan.');
header('Location: list.php');
exit;
