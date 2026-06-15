<?php
// Wajib panggil satpam dulu
require_once 'cek_sesi.php';
require_once '../config/koneksi.php';

// --- LOGIKA AUTO-GENERATE BARCODE (HANYA UNTUK TAMBAH BARU) ---
$query_last = $mysqli->query("SELECT MAX(id) as last_id FROM books");
$row_last = $query_last->fetch_assoc();
$next_id = ($row_last['last_id'] ?? 0) + 1;
// Format: BK + Tahun + ID (Contoh: BK2026001)
$auto_barcode = "BK" . date('Y') . str_pad($next_id, 3, "0", STR_PAD_LEFT);

// 1. PROSES TAMBAH / EDIT DATA BUKU
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $barcode_buku = $mysqli->real_escape_string($_POST['barcode_buku']);
    $title = $mysqli->real_escape_string($_POST['title']);
    $author = $mysqli->real_escape_string($_POST['author']);
    $category = $mysqli->real_escape_string($_POST['category']);
    $rak = $mysqli->real_escape_string($_POST['rak']);
    $description = $mysqli->real_escape_string($_POST['description']);
    $id = $_POST['id'] ?? '';
    $cover = '';

    $target_dir = "../assets/img/cover/";

    if (!empty($_FILES['cover']['name'])) {
        $cover = time() . '_' . basename($_FILES['cover']['name']);
        if (!is_dir($target_dir)) { mkdir($target_dir, 0777, true); }
        move_uploaded_file($_FILES['cover']['tmp_name'], $target_dir . $cover);
    }

    if ($id) {
        $query = "UPDATE books SET barcode_buku='$barcode_buku', title='$title', author='$author', category='$category', rak='$rak', description='$description'";
        if ($cover) $query .= ", cover='$cover'"; 
        $query .= " WHERE id=$id";
        $mysqli->query($query);
    } else {
        $stmt = $mysqli->prepare("INSERT INTO books (barcode_buku, title, author, category, rak, description, cover) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param('sssssss', $barcode_buku, $title, $author, $category, $rak, $description, $cover);
        $stmt->execute();
    }
    header('Location: books.php');
    exit;
}

// 2. PROSES HAPUS DATA BUKU
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $get_img = $mysqli->query("SELECT cover FROM books WHERE id=$id")->fetch_assoc();
    if($get_img && !empty($get_img['cover']) && file_exists("../assets/img/cover/" . $get_img['cover'])){
        unlink("../assets/img/cover/" . $get_img['cover']);
    }
    $mysqli->query("DELETE FROM books WHERE id=$id");
    header('Location: books.php');
    exit;
}

// 3. PROSES AMBIL DATA UNTUK DIEDIT
$edit_data = null;
if (isset($_GET['edit'])) {
    $id_edit = (int)$_GET['edit'];
    $edit_query = $mysqli->query("SELECT * FROM books WHERE id = $id_edit");
    if ($edit_query) {
        $edit_data = $edit_query->fetch_assoc();
    }
}

// 4. LOGIKA INTEGRASI FITUR PENCARIAN BARCODE
$cari_barcode = isset($_GET['cari_barcode']) ? $mysqli->real_escape_string($_GET['cari_barcode']) : '';
$query_books = "SELECT * FROM books";

if (!empty($cari_barcode)) {
    // Memfilter data katalog secara spesifik berdasarkan kode scan
    $query_books .= " WHERE barcode_buku = '$cari_barcode'";
}

$query_books .= " ORDER BY id DESC";
$books = $mysqli->query($query_books);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Kelola Buku - Admin SMKN 1 Kismantoro</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <style>
        .modern-card { border: none; border-radius: 16px; box-shadow: 0 10px 30px rgba(0,0,0,0.03); background: #ffffff; }
        .modern-table th { text-transform: uppercase; font-size: 0.75rem; letter-spacing: 1px; color: #a0aec0; border-bottom: 2px solid #f0f2f5; padding-bottom: 15px; }
        .modern-table td { padding: 15px 10px; border-bottom: 1px solid #f0f2f5; color: #4a5568; vertical-align: middle; }
        .modern-table tr:hover td { background-color: #f8f9fa; }
        .soft-badge { padding: 6px 12px; border-radius: 8px; font-size: 0.75rem; font-weight: 600; letter-spacing: 0.5px; }
        .btn-modern { border-radius: 50rem; transition: all 0.3s ease; font-weight: 600; }
        .btn-modern:hover { transform: translateY(-2px); }
        .img-modern { object-fit: cover; border-radius: 8px; box-shadow: 0 4px 10px rgba(0,0,0,0.1); background-color: #eee; }
        .form-control, .form-select { border-radius: 10px; padding: 10px 15px; border: 1px solid #e2e8f0; background-color: #f8f9fa; }
        .form-control:focus { background-color: #fff; border-color: #B84E32; box-shadow: 0 0 0 0.25rem rgba(184, 78, 50, 0.15); }
    </style>
</head>
<body>

    <?php include 'sidebar.php'; ?>

    <div class="main-content" style="margin-left: 260px; padding: 30px;">
        <div class="container-fluid pb-5">
            
            <div class="d-flex justify-content-between align-items-center mb-4 pb-3 border-bottom">
                <div>
                    <h3 class="fw-bold m-0 text-dark">Katalog Buku</h3>
                    <p class="text-muted m-0 mt-1 fs-6">Kelola data buku dan generate barcode otomatis</p>
                </div>
                <div class="d-flex gap-2">
                    <a href="<?= BASE_URL ?? '../' ?>index.php" target="_blank" class="btn btn-light border btn-modern shadow-sm px-4">
                        <i class="bi bi-box-arrow-up-right text-muted me-1"></i> Lihat Web
                    </a>
                </div>
            </div>

            <div class="modern-card p-4 p-lg-5 mb-5 <?= $edit_data ? 'border border-warning border-2 shadow-lg' : '' ?>">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div class="d-flex align-items-center">
                        <div class="<?= $edit_data ? 'bg-warning text-dark' : 'bg-success bg-opacity-10 text-success' ?> p-3 rounded-4 me-3">
                            <i class="bi <?= $edit_data ? 'bi-pencil-square' : 'bi-journal-plus' ?> fs-4"></i>
                        </div>
                        <div>
                            <h5 class="fw-bold mb-0 text-dark"><?= $edit_data ? 'Mode Edit Buku' : 'Tambah Buku Baru' ?></h5>
                            <small class="text-muted"><?= $edit_data ? 'Barcode tetap terkunci untuk konsistensi data.' : 'Barcode dibuat otomatis oleh sistem.' ?></small>
                        </div>
                    </div>
                    <?php if($edit_data): ?>
                        <a href="books.php" class="btn btn-sm btn-light border btn-modern px-3 text-danger fw-bold"><i class="bi bi-x-lg me-1"></i> Batal Edit</a>
                    <?php endif; ?>
                </div>

                <form method="post" enctype="multipart/form-data" class="row g-4">
                    <input type="hidden" name="id" value="<?= $edit_data['id'] ?? '' ?>">

                    <div class="col-md-3">
                        <label class="form-label small fw-bold text-primary mb-1"><i class="bi bi-upc me-1"></i> Barcode (Auto)</label>
                        <input name="barcode_buku" class="form-control border-primary bg-primary bg-opacity-10 fw-bold text-center" 
                               value="<?= $edit_data['barcode_buku'] ?? $auto_barcode ?>" readonly style="letter-spacing: 1px;">
                    </div>

                    <div class="col-md-5">
                        <label class="form-label small fw-bold text-muted mb-1">Judul Buku</label>
                        <input name="title" class="form-control" value="<?= htmlspecialchars($edit_data['title'] ?? '') ?>" placeholder="Judul Buku" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label small fw-bold text-muted mb-1">Penulis</label>
                        <input name="author" class="form-control" value="<?= htmlspecialchars($edit_data['author'] ?? '') ?>" placeholder="Nama Penulis" required>
                    </div>
                    
                    <div class="col-md-3">
                        <label class="form-label small fw-bold text-muted mb-1">Kategori</label>
                        <input name="category" class="form-control" value="<?= htmlspecialchars($edit_data['category'] ?? '') ?>" placeholder="Kategori" required>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label small fw-bold text-muted mb-1">Posisi Rak</label>
                        <input name="rak" class="form-control" value="<?= htmlspecialchars($edit_data['rak'] ?? '') ?>" placeholder="Posisi Rak" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label small fw-bold text-muted mb-1">Cover Buku</label>
                        <input type="file" name="cover" class="form-control" accept="image/*" <?= $edit_data ? '' : 'required' ?>>
                    </div>
                    
                    <div class="col-md-9">
                        <label class="form-label small fw-bold text-muted mb-1">Sinopsis</label>
                        <input name="description" class="form-control" value="<?= htmlspecialchars($edit_data['description'] ?? '') ?>" placeholder="Sinopsis singkat..." required>
                    </div>
                    <div class="col-md-3 d-flex align-items-end">
                        <button type="submit" class="btn <?= $edit_data ? 'btn-warning text-dark' : 'btn-dark' ?> btn-modern w-100 py-2 shadow-sm">
                            <i class="bi bi-save me-1"></i> <?= $edit_data ? 'Update Buku' : 'Simpan Buku' ?>
                        </button>
                    </div>
                </form>
            </div>

            <div class="modern-card p-4 p-lg-5">
                <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-4 gap-3">
                    <div class="d-flex align-items-center gap-3 flex-wrap">
                        <h5 class="fw-bold mb-0 text-dark">
                            <i class="bi bi-collection text-terakota me-2" style="color: #B84E32;"></i> Koleksi Buku
                        </h5>
                        
                        <form method="GET" action="" id="formCariBarcode" class="d-flex align-items-center">
                            <div class="input-group" style="max-width: 280px; box-shadow: 0 2px 8px rgba(0,0,0,0.02);">
                                <span class="input-group-text bg-white border-end-0" style="border-radius: 10px 0 0 10px; border-color: #e2e8f0;">
                                    <i class="bi bi-upc-scan text-muted"></i>
                                </span>
                                <input type="text" name="cari_barcode" id="cari_barcode" class="form-control border-start-0 ps-0 shadow-none" 
                                       placeholder="Scan barcode disini..." 
                                       value="<?= htmlspecialchars($cari_barcode) ?>"
                                       style="border-radius: 0 10px 10px 0; border-color: #e2e8f0; font-size: 0.85rem; background-color: #f8f9fa;" 
                                       autocomplete="off">
                                <?php if(!empty($cari_barcode)): ?>
                                    <a href="books.php" class="btn btn-light border border-start-0 d-flex align-items-center" style="border-color: #e2e8f0; border-radius: 0 10px 10px 0;">
                                        <i class="bi bi-x-circle-fill text-danger small"></i>
                                    </a>
                                <?php endif; ?>
                            </div>
                        </form>
                    </div>
                </div>
                
                <div class="table-responsive">
                    <table class="table modern-table align-middle">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Cover</th>
                                <th>Judul & Barcode</th>
                                <th>Klasifikasi</th>
                                <th class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if($books->num_rows > 0): ?>
                                <?php $no = 1; while($b = $books->fetch_assoc()): ?>
                                <tr>
                                    <td class="fw-bold text-muted"><?= $no++ ?></td>
                                    <td>
                                        <img src="../assets/img/cover/<?= htmlspecialchars($b['cover'] ?: 'default.jpg') ?>" width="45" height="60" class="img-modern">
                                    </td>
                                    <td>
                                        <span class="fw-bold text-dark d-block"><?= htmlspecialchars($b['title']) ?></span>
                                        <span class="badge bg-dark rounded-pill fw-normal mt-1"><i class="bi bi-upc me-1"></i><?= $b['barcode_buku'] ?></span>
                                    </td>
                                    <td>
                                        <div class="d-flex flex-column gap-1">
                                            <span class="soft-badge bg-primary bg-opacity-10 text-primary border border-primary border-opacity-10 text-center"><?= htmlspecialchars($b['category']) ?></span>
                                            <span class="soft-badge bg-secondary bg-opacity-10 text-secondary border border-secondary border-opacity-10 text-center">Rak: <?= htmlspecialchars($b['rak']) ?></span>
                                        </div>
                                    </td>
                                    <td class="text-center">
                                        <div class="d-flex gap-2 justify-content-center">
                                            <button type="button" class="btn btn-sm btn-dark btn-modern px-3 btn-pemicu-label" 
                                                    data-title="<?= htmlspecialchars($b['title']) ?>" 
                                                    data-barcode="<?= htmlspecialchars($b['barcode_buku']) ?>">
                                                <i class="bi bi-printer"></i> Label
                                            </button>
                                            <a href="?edit=<?= $b['id'] ?>" class="btn btn-sm btn-light border text-primary btn-modern px-3">
                                                <i class="bi bi-pencil"></i>
                                            </a>
                                            <a href="?delete=<?= $b['id'] ?>" onclick="return confirm('Hapus buku ini?')" class="btn btn-sm btn-light border text-danger btn-modern px-2">
                                                <i class="bi bi-trash"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="5" class="text-center py-5 text-muted">
                                        <i class="bi bi-search display-6 opacity-25 d-block mb-2"></i>
                                        Buku dengan barcode <code class="text-danger"><?= htmlspecialchars($cari_barcode) ?></code> tidak ditemukan.
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>

    <div class="modal fade" id="modalCetakLabel" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" style="max-width: 360px;">
            <div class="modal-content" style="border-radius: 16px; border: none; box-shadow: 0 15px 50px rgba(0,0,0,0.1);">
                <div class="modal-header border-0 pb-0">
                    <h5 class="fw-bold text-dark m-0">Label Barcode</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body text-center d-flex flex-column align-items-center justify-content-center py-4">
                    
                    <div id="areaLabelModal" style="border: 1px solid #000; padding: 15px; width: 220px; text-align: center; background-color: #ffffff; box-sizing: border-box; margin-bottom: 20px;">
                        <strong id="lbl_judul" style="display: block; font-size: 14px; margin-bottom: 8px; word-wrap: break-word; color: #000;">-</strong>
                        <img id="lbl_barcode_img" src="" alt="Barcode" style="max-width: 100%; height: auto; margin: 5px 0;">
                        <code id="lbl_barcode_text" style="display: block; font-size: 13px; font-weight: bold; margin-top: 5px; letter-spacing: 1px; color: #000;">-</code>
                    </div>

                    <button id="btnUnduhModal" class="btn w-100 py-2.5 fw-bold text-white shadow-sm" style="background-color: #B84E32; border: none; border-radius: 10px; transition: background 0.2s;">
                        <i class="bi bi-download me-2"></i>Download Gambar Label
                    </button>
                </div>
            </div>
        </div>
    </div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
$(document).ready(function() {
    // 1. Logika Autofocus Form Pencarian Barcode Buku
    $('#cari_barcode').focus();
    $('#cari_barcode').on('change', function() {
        if($(this).val().trim() !== "") {
            $('#formCariBarcode').submit();
        }
    });

    // 2. Logika Interaksi Jendela Pop-up Modal Label
    let namaFileDownload = "Label_Buku.png";

    $('.btn-pemicu-label').on('click', function() {
        const judulBuku = $(this).data('title');
        const barcodeBuku = $(this).data('barcode');
        
        // Buat format nama file unduhan (Spasi diganti dengan Underscore)
        namaFileDownload = "Label_" + judulBuku.replace(/\s+/g, '_') + ".png";

        // Ganti data di dalam modal secara real-time
        $('#lbl_judul').text(judulBuku);
        $('#lbl_barcode_text').text(barcodeBuku);
        
        // Panggil endpoint API untuk generate barcode
        const urlBarcodeApi = "https://bwipjs-api.metafloor.com/?bcid=code128&text=" + encodeURIComponent(barcodeBuku) + "&scale=2&rotate=N&crossOrigin=anonymous";
        $('#lbl_barcode_img').attr('src', urlBarcodeApi);

        // Munculkan Pop-up Modal
        $('#modalCetakLabel').modal('show');
    });

    // 3. Logika Pembuatan Gambar PNG via html2canvas
    $('#btnUnduhModal').on('click', function() {
        const areaLabel = document.getElementById('areaLabelModal');

        html2canvas(areaLabel, {
            useCORS: true, 
            scale: 3, // Kualitas gambar tinggi (tidak pecah saat dicetak ke stiker)
            backgroundColor: "#ffffff"
        }).then(function(canvas) {
            let link = document.createElement('a');
            link.download = namaFileDownload;
            link.href = canvas.toDataURL('image/png');
            link.click();
        });
    });
});
</script>
</body>
</html>