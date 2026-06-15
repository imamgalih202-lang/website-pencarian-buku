<?php
// 1. Hubungkan ke database dan core sistem
require_once '../config/koneksi.php';
require_once '../core/cbf_engine.php';

// 2. Simulasi Session Siswa (Jika Anda ingin menambahkan login siswa nanti)
// Untuk skripsi, kita buat sambutan umum yang elegan
$nama_siswa = "Sobat Literasi"; 

// 3. Ambil data untuk statistik ringkas
$total_buku = $mysqli->query("SELECT COUNT(*) as total FROM books")->fetch_assoc()['total'];
$total_blog = $mysqli->query("SELECT COUNT(*) as total FROM blog_posts")->fetch_assoc()['total'];

// 4. Ambil 4 Buku Acak untuk memicu mesin rekomendasi (Simulasi minat awal)
$random_book = $mysqli->query("SELECT id FROM books ORDER BY RAND() LIMIT 1")->fetch_assoc();
$id_pemicu = $random_book['id'] ?? 0;

// Jalankan mesin CBF untuk memberikan rekomendasi di dashboard
$rekomendasi_ai = getRekomendasiBuku($mysqli, $id_pemicu, 4);

// 5. Panggil template
include '../includes/header.php';
include '../includes/navbar.php';
?>

<main class="flex-shrink-0">
    <section class="py-5" style="background: linear-gradient(135deg, #fdfbfb 0%, #ebedee 100%); border-bottom: 1px solid #e5dfd3;">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <h2 class="fw-bold text-dark mb-2">Halo, <?= $nama_siswa ?>! 👋</h2>
                    <p class="text-muted fs-5">Selamat datang di Perpustakaan Digital SMK N 1 Kismantoro. Apa yang ingin kamu pelajari hari ini?</p>
                </div>
                <div class="col-md-4 text-md-end">
                    <div class="d-inline-flex gap-3 mt-3 mt-md-0">
                        <div class="text-center p-3 bg-white rounded shadow-sm border">
                            <h4 class="fw-bold mb-0 text-dark"><?= $total_buku ?></h4>
                            <small class="text-muted">Koleksi Buku</small>
                        </div>
                        <div class="text-center p-3 bg-white rounded shadow-sm border">
                            <h4 class="fw-bold mb-0 text-dark"><?= $total_blog ?></h4>
                            <small class="text-muted">Artikel Blog</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <div class="container py-5">
        <div class="mb-5">
            <div class="d-flex align-items-center mb-4">
                <h4 class="fw-bold m-0 text-dark"><i class="bi bi-magic text-warning me-2"></i> Rekomendasi Spesial Untukmu</h4>
                <hr class="flex-grow-1 ms-3 opacity-10">
            </div>

            <div class="row g-4">
                <?php if (empty($rekomendasi_ai)): ?>
                    <div class="col-12">
                        <div class="p-5 text-center bg-white rounded border border-dashed">
                            <i class="bi bi-book-half display-4 text-muted opacity-25 mb-3"></i>
                            <p class="text-muted">Mulai jelajahi katalog untuk mendapatkan rekomendasi yang lebih akurat.</p>
                            <a href="<?= BASE_URL ?>index.php" class="btn btn-outline-dark btn-sm">Lihat Katalog</a>
                        </div>
                    </div>
                <?php else: ?>
                    <?php foreach ($rekomendasi_ai as $rek): ?>
                        <div class="col-6 col-md-4 col-lg-3">
                            <div class="card card-custom h-100">
                                <a href="detail_buku.php?id=<?= $rek['id'] ?>">
                                    <img src="<?= BASE_URL ?>assets/img/cover/<?= htmlspecialchars($rek['cover'] ?: 'default.jpg') ?>" class="card-img-top w-100" alt="<?= htmlspecialchars($rek['title']) ?>">
                                </a>
                                <div class="card-body px-0 pt-3 pb-0">
                                    <div class="d-flex justify-content-between align-items-start mb-1">
                                        <h6 class="fw-bold text-dark mb-0 pe-2"><?= htmlspecialchars($rek['title']) ?></h6>
                                        <span class="badge bg-success bg-opacity-10 text-success small border border-success border-opacity-10">AI Match</span>
                                    </div>
                                    <p class="small text-muted mb-0"><?= htmlspecialchars($rek['author'] ?? 'Anonim') ?></p>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>

        <div class="row g-4 mt-2">
            <div class="col-md-6">
                <div class="p-4 rounded-4 bg-dark text-white shadow-sm h-100 d-flex flex-column justify-content-between">
                    <div>
                        <h5 class="fw-bold mb-3"><i class="bi bi-journal-text text-warning"></i> Jelajahi Literasi</h5>
                        <p class="small opacity-75">Baca artikel terbaru mengenai tips belajar, resensi buku, dan berita terkini seputar dunia pendidikan.</p>
                    </div>
                    <a href="blog.php" class="btn btn-warning btn-sm fw-bold w-100 mt-3">Buka Blog</a>
                </div>
            </div>
            <div class="col-md-6">
                <div class="p-4 rounded-4 bg-white border shadow-sm h-100 d-flex flex-column justify-content-between">
                    <div>
                        <h5 class="fw-bold mb-3 text-dark"><i class="bi bi-search text-primary"></i> Pencarian Canggih</h5>
                        <p class="small text-muted">Gunakan fitur pencarian untuk menemukan buku berdasarkan kategori, penulis, atau lokasi rak tertentu.</p>
                    </div>
                    <form action="pencarian.php" method="get" class="mt-3">
                        <div class="input-group">
                            <input type="text" name="q" class="form-control form-control-sm border-end-0" placeholder="Cari sesuatu...">
                            <button class="btn btn-outline-dark btn-sm border-start-0 px-3">🔍</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</main>

<style>
    .border-dashed { border-style: dashed !important; border-width: 2px !important; }
</style>

<?php include '../includes/footer.php'; ?>