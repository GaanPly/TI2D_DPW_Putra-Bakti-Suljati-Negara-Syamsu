<?php
$page_title = "Daftar Senjata";
include __DIR__ . '/../includes/header.php';
require __DIR__ . '/../includes/koneksi.php';

$flash = $_SESSION['flash'] ?? null;
unset($_SESSION['flash']);

$daftarSenjata = $pdo->query("SELECT * FROM senjata ORDER BY id DESC")->fetchAll(PDO::FETCH_ASSOC);
?>
        <section>
            <h2>Daftar Senjata</h2>

            <?php if ($flash): ?>
                <p class="flash flash-<?php echo $flash['type']; ?>"><?php echo $flash['pesan']; ?></p>
            <?php endif; ?>

            <div class="search-box">
                <label for="search-input">Cari Nama Senjata</label>
                <input type="text" id="search-input" placeholder="Ketik nama senjata...">
            </div>

            <div class="table-responsive">
            <table>
                <thead>
                    <tr>
                        <th>Nama</th>
                        <th>Deskripsi</th>
                        <th>Kerusakan</th>
                        <th>Durabilitas</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($daftarSenjata)): ?>
                    <tr>
                        <td colspan="5">Belum ada data senjata. Silakan tambah lewat menu "Tambah Senjata".</td>
                    </tr>
                    <?php else: ?>
                        <?php foreach ($daftarSenjata as $senjata): ?>
                        <tr>
                            <td><?php echo $senjata['nama']; ?></td>
                            <td><?php echo $senjata['deskripsi']; ?></td>
                            <td><?php echo $senjata['kerusakan']; ?></td>
                            <td><?php echo $senjata['durabilitas']; ?></td>
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
