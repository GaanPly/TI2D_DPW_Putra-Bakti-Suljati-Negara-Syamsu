<?php
$page_title = "Tambah Senjata";
include __DIR__ . '/../includes/header.php';

$flash = $_SESSION['flash'] ?? null;
unset($_SESSION['flash']);
?>
        <section>
            <h2>Tambah Senjata</h2>

            <?php if ($flash): ?>
                <p class="flash flash-<?php echo $flash['type']; ?>"><?php echo $flash['pesan']; ?></p>
            <?php endif; ?>

            <form id="form-tambah" method="post" action="proses_tambah.php">
                <p>
                    <label for="nama">Nama</label><br>
                    <input type="text" id="nama" name="nama" required>
                </p>
                <p>
                    <label for="deskripsi">Deskripsi</label><br>
                    <input type="text" id="deskripsi" name="deskripsi" required>
                </p>
                <p>
                    <label for="kerusakan">Kerusakan</label><br>
                    <input type="number" id="kerusakan" name="kerusakan" min="0" required>
                </p>
                <p>
                    <label for="durabilitas">Durabilitas</label><br>
                    <input type="number" id="durabilitas" name="durabilitas" min="0" required>
                </p>
                <p>
                    <button type="submit">Simpan</button>
                </p>
            </form>
        </section>
<?php include __DIR__ . '/../includes/footer.php'; ?>
