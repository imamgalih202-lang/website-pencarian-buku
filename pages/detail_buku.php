<?php
require_once '../config/koneksi.php';
// Panggil otak algoritma rekomendasi
require_once '../core/cbf_engine.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Ambil detail buku yang sedang dibuka
$query = $mysqli->query("SELECT * FROM books WHERE id = $id");
$book = $query->fetch_assoc();

// Jika buku tidak ditemukan, kembalikan ke index
if (!$book) {
    header("Location: ../index.php");
    exit;
}

// 🚀 JALANKAN MESIN REKOMENDASI MACHINE LEARNING
$rekomendasi = getRekomendasiBuku($mysqli, $id, 4);

include '../includes/header.php'; 
?>

<div style="height: 100px; width: 100%;"></div>

<main class="flex-shrink-0 pb-5">
    <div class="container">
        
        <nav aria-label="breadcrumb" class="mb-4"> 
            <ol class="breadcrumb bg-transparent p-0 m-0 fs-6">
                <li class="breadcrumb-item">
                    <a href="<?= BASE_URL ?>index.php" class="text-decoration-none text-muted">
                        <i class="bi bi-house-door me-1"></i>Beranda
                    </a>
                </li>
                <li class="breadcrumb-item">
                    <a href="pencarian.php?kategori=<?= urlencode($book['category']) ?>" class="text-decoration-none text-muted">
                        <?= htmlspecialchars($book['category']) ?>
                    </a>
                </li>
                <li class="breadcrumb-item active fw-semibold text-dark" aria-current="page">
                    <?= htmlspecialchars($book['title']) ?>
                </li>
            </ol>
        </nav>

        <div class="modern-card p-4 p-lg-5 mb-5 border-0 shadow-sm" style="background-color: #ffffff; border-radius: 20px;">
            <div class="row align-items-center">
                <div class="col-md-4 col-lg-3 text-center mb-4 mb-md-0">
                    <div class="position-relative d-inline-block">
                        <img src="<?= BASE_URL ?>assets/img/cover/<?= htmlspecialchars($book['cover'] ?: 'default.jpg') ?>" 
                             alt="<?= htmlspecialchars($book['title']) ?>" 
                             class="img-fluid rounded-4" 
                             style="max-height: 450px; object-fit: cover; box-shadow: 0 15px 35px rgba(0,0,0,0.15);">
                    </div>
                </div>
                
                <div class="col-md-8 col-lg-9 ps-md-4 ps-lg-5">
                    <h1 class="fw-bolder mb-3 text-dark" style="letter-spacing: -0.5px;"><?= htmlspecialchars($book['title']) ?></h1>
                    
                    <div class="d-flex align-items-center mb-4">
                        <img src="https://ui-avatars.com/api/?name=<?= urlencode($book['author'] ?? 'A') ?>&background=random&color=fff&bold=true" class="rounded-circle me-2 shadow-sm" width="35" height="35">
                        <span class="fs-5 fw-bold" style="color: var(--primary-terra);"><?= htmlspecialchars($book['author'] ?? 'Anonim') ?></span>
                    </div>
                    
                    <div class="d-flex flex-wrap gap-2 mb-4">
                        <span class="soft-badge bg-primary bg-opacity-10 text-primary border border-primary border-opacity-25 px-3 py-2 fs-6" style="border-radius: 10px; display: inline-block;">
                            <i class="bi bi-tag-fill me-1"></i> <?= htmlspecialchars($book['category']) ?>
                        </span>
                        <span class="soft-badge bg-secondary bg-opacity-10 text-secondary border border-secondary border-opacity-25 px-3 py-2 fs-6" style="border-radius: 10px; display: inline-block;">
                            <i class="bi bi-geo-alt-fill me-1 text-danger"></i> Rak: <?= htmlspecialchars($book['rak'] ?? 'Belum diatur') ?>
                        </span>
                        <?php if(!empty($book['barcode_buku'])): ?>
                        <span class="badge bg-dark px-3 py-2 fs-6 fw-normal font-monospace rounded-pill d-flex align-items-center">
                            <i class="bi bi-upc-scan me-2"></i><?= htmlspecialchars($book['barcode_buku']) ?>
                        </span>
                        <?php endif; ?>
                    </div>
                    
                    <div class="mt-2">
                        <h5 class="fw-bold text-dark mb-3"><i class="bi bi-card-text text-muted me-2"></i>Sinopsis</h5>
                        <div class="p-4 rounded-4 bg-light bg-opacity-50 border border-light" style="border-radius: 15px;">
                            <p class="text-secondary m-0" style="line-height: 1.8; text-align: justify; font-size: 1.05rem;">
                                <?= nl2br(htmlspecialchars($book['description'])) ?>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="mt-5 pt-3">
            <div class="d-flex align-items-center mb-4">
                <div class="bg-warning bg-opacity-10 p-2 rounded-3 me-3 text-warning border border-warning border-opacity-25">
                    <i class="bi bi-cpu-fill fs-4"></i>
                </div>
                <div>
                    <h4 class="fw-bolder mb-0 text-dark">Rekomendasi Cerdas AI</h4>
                    <p class="text-muted small m-0">Berdasarkan kemiripan konten (Content-Based Filtering)</p>
                </div>
            </div>
            
            <div class="row g-4">
                <?php if (empty($rekomendasi)): ?>
                    <div class="col-12 text-center py-5">
                        <h5 class="text-muted fw-semibold">Belum ada rekomendasi yang cocok.</h5>
                    </div>
                <?php else: ?>
                    <?php foreach ($rekomendasi as $rek): ?>
                        <div class="col-6 col-md-4 col-lg-3">
                            <div class="card card-custom h-100" style="cursor: pointer;" onclick="window.location.href='detail_buku.php?id=<?= $rek['id'] ?>'">
                                <div class="card-img-wrapper position-relative">
                                    <img src="<?= BASE_URL ?>assets/img/cover/<?= htmlspecialchars($rek['cover'] ?: 'default.jpg') ?>" class="card-img-top w-100" alt="<?= htmlspecialchars($rek['title']) ?>">
                                    <div class="position-absolute top-0 end-0 m-2">
                                        <span class="badge bg-success bg-opacity-75 px-2 py-1 shadow-sm" style="backdrop-filter: blur(4px);">
                                            Match <?= round($rek['skor_kemiripan'] * 100) ?>%
                                        </span>
                                    </div>
                                </div>
                                <div class="card-body px-1 pt-3 pb-0 text-center">
                                    <h6 class="fw-bold text-dark mb-1 book-title"><?= htmlspecialchars($rek['title']) ?></h6>
                                    <p class="small text-muted mb-0"><i class="bi bi-pen me-1"></i><?= htmlspecialchars($rek['author'] ?? 'Anonim') ?></p>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>

    </div>
</main>

<?php include '../includes/footer.php'; ?>