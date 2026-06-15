<?php
// 1. Hubungkan ke database dan konfigurasi dasar (Path mengarah ke folder config)
require_once 'config/koneksi.php';

// 2. Ambil 1 buku terbaru untuk Hero Section (Sorotan Utama)
$featured_result = $mysqli->query("SELECT * FROM books ORDER BY id DESC LIMIT 1");
$featured_book = $featured_result->fetch_assoc();

// 3. Ambil 4 buku lainnya untuk "Koleksi Lainnya" 
// (Mengecualikan buku yang sudah tampil di Hero Section)
$featured_id = $featured_book['id'] ?? 0;
$result = $mysqli->query("SELECT * FROM books WHERE id != $featured_id ORDER BY id DESC LIMIT 4");
$books = $result->fetch_all(MYSQLI_ASSOC);
?>

<?php include 'includes/header.php'; ?>


<main class="flex-shrink-0">

    <?php if ($featured_book): ?>
    <section class="hero-section container py-5">
        <div class="row align-items-center">
            <div class="col-md-4 position-relative mb-4 mb-md-0 text-center">
                <img src="assets/img/cover/<?= htmlspecialchars($featured_book['cover'] ?: 'default.jpg') ?>" class="book-cover-hero" alt="<?= htmlspecialchars($featured_book['title']) ?>">
                <span class="badge badge-hot px-3 py-2">HOT</span>
            </div>
            
            <div class="col-md-7 offset-md-1">
                <h1 class="fw-bold display-5 mb-2 text-dark"><?= htmlspecialchars($featured_book['title']) ?></h1>
                <p class="text-muted mb-3">
                    <span class="fw-bold" style="color: #B84E32;"><?= htmlspecialchars($featured_book['author'] ?? 'Anonim') ?></span> • 
                    Kategori: <?= htmlspecialchars($featured_book['category'] ?? '-') ?>
                </p>
                
                <span class="badge bg-light text-dark border mb-4 px-3 py-2">Rak: <?= htmlspecialchars($featured_book['rak'] ?? 'Belum diatur') ?></span>
                
                <h5 class="fw-bold mb-3 text-dark">Sinopsis</h5>
                <p style="line-height: 1.8; color: #555;">
                    <?= htmlspecialchars(substr($featured_book['description'], 0, 300)) ?>...
                </p>
                
                <a href="pages/detail_buku.php?id=<?= $featured_book['id'] ?>" class="btn btn-orange btn-lg px-5 mt-3">Baca Sekarang</a>
            </div>
        </div>
    </section>
    <?php endif; ?>

    <div class="container pb-5">
        <h4 class="fw-bold mb-4 text-dark">📚 Koleksi Lainnya</h4>
        <div class="row g-4">
            <?php if (empty($books) && !$featured_book): ?>
            <div class="col-12 text-center">
                <div class="alert alert-secondary text-dark">Belum ada data buku yang tersedia.</div>
            </div>
            <?php else: ?>
            <?php foreach ($books as $book): ?>
                <div class="col-6 col-md-4 col-lg-3">
                    <div class="card card-custom h-100">
                        <a href="pages/detail_buku.php?id=<?= $book['id'] ?>">
                            <img src="assets/img/cover/<?= htmlspecialchars($book['cover'] ?: 'default.jpg') ?>" class="card-img-top w-100" alt="<?= htmlspecialchars($book['title']) ?>">
                        </a>
                        <div class="card-body px-0 pt-3 pb-0">
                            <h6 class="fw-bold text-dark mb-1"><?= htmlspecialchars($book['title']) ?></h6>
                            <p class="small text-muted mb-0"><?= htmlspecialchars($book['author'] ?? 'Anonim') ?></p>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>

</main>
<?php include 'includes/footer.php'; ?>