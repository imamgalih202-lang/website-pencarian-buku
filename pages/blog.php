<?php
require_once '../config/koneksi.php';

// Ambil semua data blog
$query = $mysqli->query("SELECT * FROM blog_posts ORDER BY tanggal_publikasi DESC");
$posts = $query->fetch_all(MYSQLI_ASSOC);

include '../includes/header.php';
include '../includes/navbar.php';
?>

<main class="flex-shrink-0 py-5">
    <div class="container mb-5 text-center">
        <h2 class="fw-bold text-dark">Blog & Artikel Literasi</h2>
        <p class="text-muted">Kumpulan berita, resensi, dan karya tulis dari Perpustakaan SMKN 1 Kismantoro.</p>
    </div>

    <div class="container pb-5">
        <div class="row g-4">
            <?php if (empty($posts)): ?>
                <div class="col-12 text-center mt-5">
                    <h5 class="text-muted">Belum ada artikel yang dipublikasikan.</h5>
                </div>
            <?php else: ?>
                <?php foreach ($posts as $post): ?>
                    <div class="col-md-6 col-lg-4">
                        <div class="card border-0 shadow-sm h-100 overflow-hidden" style="transition: transform 0.3s; background-color: #fff;">
                            <img src="<?= BASE_URL ?>assets/img/blog/<?= htmlspecialchars($post['gambar_post'] ?: 'default_blog.jpg') ?>" 
                                 class="card-img-top" 
                                 style="height: 220px; object-fit: cover;" 
                                 alt="Gambar Blog">
                            
                            <div class="card-body p-4 d-flex flex-column">
                                <span class="badge bg-light text-muted mb-3 align-self-start border">
                                    <i class="bi bi-calendar3"></i> <?= date('d M Y', strtotime($post['tanggal_publikasi'])) ?>
                                </span>
                                
                                <a href="baca_blog.php?id=<?= $post['id_post'] ?>" class="text-decoration-none text-dark">
                                    <h5 class="fw-bold mb-3 hover-terakota"><?= htmlspecialchars($post['judul_post']) ?></h5>
                                </a>
                                
                                <p class="text-muted small mb-4 flex-grow-1">
                                    <?= htmlspecialchars(substr($post['isi_post'], 0, 150)) ?>...
                                </p>
                                
                                <a href="baca_blog.php?id=<?= $post['id_post'] ?>" class="btn btn-outline-dark w-100 mt-auto fw-bold">Baca Selengkapnya</a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</main>

<style>
    /* Tambahan efek hover khusus halaman ini */
    .card:hover { transform: translateY(-5px); box-shadow: 0 10px 20px rgba(0,0,0,0.1) !important; }
    .hover-terakota:hover { color: #B84E32 !important; }
</style>

<?php include '../includes/footer.php'; ?>