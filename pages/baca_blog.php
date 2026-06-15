<?php
require_once '../config/koneksi.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

$query = $mysqli->query("SELECT * FROM blog_posts WHERE id_post = $id");
$post = $query->fetch_assoc();

if (!$post) {
    header("Location: blog.php");
    exit;
}

include '../includes/header.php';
include '../includes/navbar.php';
?>

<main class="flex-shrink-0 py-5">
    <div class="container pb-5">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <a href="blog.php" class="text-decoration-none text-muted mb-4 d-inline-block">
                    <i class="bi bi-arrow-left"></i> Kembali ke Daftar Artikel
                </a>

                <h1 class="fw-bold text-dark mb-3"><?= htmlspecialchars($post['judul_post']) ?></h1>
                <div class="text-muted mb-4 pb-3 border-bottom">
                    <i class="bi bi-calendar-event me-2"></i> Dipublikasikan pada <?= date('d F Y', strtotime($post['tanggal_publikasi'])) ?>
                </div>

                <img src="<?= BASE_URL ?>assets/img/blog/<?= htmlspecialchars($post['gambar_post'] ?: 'default_blog.jpg') ?>" 
                     class="img-fluid rounded shadow-sm mb-5 w-100" 
                     style="max-height: 400px; object-fit: cover;" 
                     alt="Gambar Artikel">

                <div class="artikel-konten" style="line-height: 1.9; font-size: 1.05rem; color: #444; text-align: justify;">
                    <?= nl2br(htmlspecialchars($post['isi_post'])) ?>
                </div>
                
                <div class="mt-5 pt-4 border-top">
                    <p class="fw-bold mb-2">Bagikan artikel ini:</p>
                    <button class="btn btn-light border text-primary me-2"><i class="bi bi-facebook"></i> Facebook</button>
                    <button class="btn btn-light border text-info me-2"><i class="bi bi-twitter"></i> Twitter</button>
                    <button class="btn btn-light border text-success"><i class="bi bi-whatsapp"></i> WhatsApp</button>
                </div>
            </div>
        </div>
    </div>
</main>

<?php include '../includes/footer.php'; ?>