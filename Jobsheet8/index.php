<?php
$page_title = "Beranda";
include __DIR__ . '/includes/header.php';
require __DIR__ . '/includes/koneksi.php';

$totalSenjata = $pdo->query("SELECT COUNT(*) FROM senjata")->fetchColumn();
$totalKarakter = $pdo->query("SELECT COUNT(*) FROM karakter")->fetchColumn();
?>
        <section>
            <h2>Selamat Datang di Sistem Database Game</h2>
            <p>Aplikasi sederhana untuk mengelola data senjata dan karakter.</p>
        </section>

        <section>
            <h2>Ringkasan</h2>
        </section>
        <section class="summary">
            <article>
                <h3>Total Senjata</h3>
                <p><?php echo $totalSenjata; ?></p>
            </article>
            <article>
                <h3>Total Karakter</h3>
                <p><?php echo $totalKarakter; ?></p>
            </article>
        </section>
<?php include __DIR__ . '/includes/footer.php'; ?>
