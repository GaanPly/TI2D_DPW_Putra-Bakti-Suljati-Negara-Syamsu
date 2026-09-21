<?php
$page_title = "Tambah Karakter";
include __DIR__ . '/../includes/header.php';

?>
        <section>
            <h2>Tambah Karakter</h2>

            <?php if ($flash): ?>
                <p class="flash flash-<?php echo e($flash['type']); ?>"><?php echo e($flash['pesan']); ?></p>
            <?php endif; ?>

            <form id="form-tambah" method="post" action="proses_tambah.php">
                <p>
                    <label for="nama">Nama</label><br>
                    <input type="text" id="nama" name="nama" required>
                </p>
                <p>
                    <label for="nyawa">Nyawa</label><br>
                    <input type="number" id="nyawa" name="nyawa" min="0" required>
                </p>
                <p>
                    <label for="perlindungan">Perlindungan</label><br>
                    <input type="number" id="perlindungan" name="perlindungan" min="0" required>
                </p>
                <p>
                    <button type="submit">Simpan</button>
                </p>
            </form>
        </section>
<?php include __DIR__ . '/../includes/footer.php'; ?>
