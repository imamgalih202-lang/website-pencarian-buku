<?php
require_once '../config/koneksi.php';

$q = isset($_GET['q']) ? $mysqli->real_escape_string(trim($_GET['q'])) : '';
$kategori = isset($_GET['kategori']) ? $mysqli->real_escape_string(trim($_GET['kategori'])) : '';
$penulis = isset($_GET['penulis']) ? $mysqli->real_escape_string(trim($_GET['penulis'])) : '';

// Menyusun logika SQL dinamis berdasarkan filter yang ada
$sql = "SELECT * FROM books WHERE 1=1";
$judul_halaman = "Semua Koleksi Buku";

if ($q !== '') {
    $sql .= " AND (title LIKE '%$q%' OR author LIKE '%$q%' OR description LIKE '%$q%')";
    $judul_halaman = "Hasil pencarian: \"". htmlspecialchars($q) ."\"";
} elseif ($kategori !== '') {
    $sql .= " AND category = '$kategori'";
    $judul_halaman = "Kategori: " . htmlspecialchars($kategori);
} elseif ($penulis !== '') {
    $sql .= " AND author = '$penulis'";
    $judul_halaman = "Karya: " . htmlspecialchars($penulis);
}

$sql .= " ORDER BY id DESC";
$result = $mysqli->query($sql);
$books = $result->fetch_all(MYSQLI_ASSOC);

include '../includes/header.php';
include '../includes/navbar.php';
?>

<main class="flex-shrink-0 py-5">
    <div class="container">
        <h3 class="fw-bold mb-4 pb-2 border-bottom text-dark"><?= $judul_halaman ?></h3>
        
        <div class="row g-4 mb-5">
            <?php if (empty($books)): ?>
                <div class="col-12 text-center py-5">
                    <i class="bi bi-search display-1 text-muted opacity-50 mb-3 d-block"></i>
                    <h5 class="text-muted">Maaf, buku tidak ditemukan.</h5>
                    <p class="text-muted">Coba gunakan kata kunci lain atau periksa ejaan Anda.</p>
                    <a href="<?= BASE_URL ?>index.php" class="btn btn-orange mt-3">Kembali ke Beranda</a>
                </div>
            <?php else: ?>
                <?php foreach ($books as $book): ?>
                    <div class="col-6 col-md-4 col-lg-3">
                        <div class="card card-custom h-100">
                            <a href="detail_buku.php?id=<?= $book['id'] ?>">
                                <img src="<?= BASE_URL ?>assets/img/cover/<?= htmlspecialchars($book['cover'] ?: 'default.jpg') ?>" class="card-img-top w-100" alt="<?= htmlspecialchars($book['title']) ?>">
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

<?php include '../includes/footer.php'; ?>