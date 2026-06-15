<?php
require_once 'cek_sesi.php';
require_once '../config/koneksi.php';

$pesan = '';

// Simpan konfigurasi baru jika form dikirim
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['simpan_aturan'])) {
    $batas_baru = (int)$_POST['batas_hari_pinjam'];
    if ($batas_baru > 0) {
        $mysqli->query("UPDATE konfigurasi SET batas_hari_pinjam = $batas_baru WHERE id = 1");
        $pesan = '<div class="alert alert-success border-0 shadow-sm rounded-4"><i class="bi bi-check-circle-fill me-2"></i>Batas waktu sirkulasi buku berhasil diubah menjadi ' . $batas_baru . ' hari!</div>';
    }
}

// Ambil data konfigurasi saat ini
$konfig = $mysqli->query("SELECT batas_hari_pinjam FROM konfigurasi WHERE id = 1")->fetch_assoc();
$batas_sekarang = isset($konfig['batas_hari_pinjam']) ? $konfig['batas_hari_pinjam'] : 7;
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Konfigurasi Aturan Perpustakaan</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <style>
        body { font-family: 'Poppins', sans-serif; background-color: #f8f9fa; }
        .main-content { margin-left: 260px; padding: 30px; }
        .modern-card { border: none; border-radius: 16px; box-shadow: 0 10px 30px rgba(0,0,0,0.03); background: #ffffff; padding: 2rem; max-width: 500px; }
    </style>
</head>
<body>
    <?php include 'sidebar.php'; ?>
    <div class="main-content">
        <div class="container-fluid">
            <h3 class="fw-bold text-dark mb-4">Pengaturan Batas Sirkulasi Buku</h3>
            <?= $pesan; ?>
            <div class="modern-card mt-3">
                <form method="POST" action="">
                    <div class="mb-4">
                        <label class="form-label small fw-bold text-muted mb-2">BATAS WAKTU MAKSIMAL PEMINJAMAN</label>
                        <div class="input-group">
                            <input type="number" name="batas_hari_pinjam" class="form-control" value="<?= $batas_sekarang; ?>" min="1" required>
                            <span class="input-group-text bg-light fw-bold">Hari</span>
                        </div>
                        <small class="text-muted d-block mt-2">Aturan ini akan mengontrol kalkulasi durasi otomatis jatuh tempo pengembalian buku dan denda sanksi keterlambatan.</small>
                    </div>
                    <button type="submit" name="simpan_aturan" class="btn btn-dark w-100 py-2.5 rounded-pill fw-bold">
                        <i class="bi bi-save2 me-2"></i> Perbarui Ketentuan
                    </button>
                </form>
            </div>
        </div>
    </div>
</body>
</html>