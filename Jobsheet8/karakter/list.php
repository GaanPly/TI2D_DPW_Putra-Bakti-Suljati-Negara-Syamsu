<?php
$page_title = "Daftar Karakter";
include __DIR__ . '/../includes/header.php';
require __DIR__ . '/../includes/koneksi.php';

$flash = $_SESSION['flash'] ?? null;
unset($_SESSION['flash']);

$daftarKarakter = $pdo->query("SELECT * FROM karakter ORDER BY id DESC")->fetchAll(PDO::FETCH_ASSOC);
?>
        <section>
            <h2>Daftar Karakter</h2>

            <?php if ($flash): ?>
                <p class="flash flash-<?php echo $flash['type']; ?>"><?php echo $flash['pesan']; ?></p>
            <?php endif; ?>

            <div class="search-box">
                <label for="search-input">Cari Nama Karakter</label>
                <input type="text" id="search-input" placeholder="Ketik nama karakter...">
            </div>

            <div class="table-responsive">
            <table>
                <thead>
                    <tr>
                        <th>No. Karakter</th>
                        <th>Nama</th>
                        <th>Nyawa</th>
                        <th>Perlindungan</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($daftarKarakter)): ?>
                    <tr>
                        <td colspan="5">Belum ada data karakter. Silakan tambah lewat menu "Tambah Karakter".</td>
                    </tr>
                    <?php else: ?>
                        <?php foreach ($daftarKarakter as $karakter): ?>
                        <tr>
                            <td><?php echo $karakter['id']; ?></td>
                            <td><?php echo $karakter['nama']; ?></td>
                            <td><?php echo $karakter['nyawa']; ?></td>
                            <td><?php echo $karakter['perlindungan']; ?></td>
                            <td>
                                <button type="button">Edit</button>
                                <button type="button" class="btn-hapus">Hapus</button>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
            </div>
        </section>
<?php include __DIR__ . '/../includes/footer.php'; ?>
