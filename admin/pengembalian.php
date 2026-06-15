<?php
require_once 'cek_sesi.php';
require_once '../config/koneksi.php';

// 1. PROSES UPDATE STATUS PENGEMBALIAN (EKSEKUSI FORM)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['konfirmasi_kembali'])) {
    $id_peminjaman = (int)$_POST['id_peminjaman'];
    
    // Update status peminjaman menjadi dikembalikan
    $mysqli->query("UPDATE peminjaman SET status = 'Dikembalikan' WHERE id = $id_peminjaman");
    
    header('Location: pengembalian.php?pesan=sukses');
    exit;
}

// 2. AMBIL DATA UNTUK TABEL DAFTAR TUNGGU (DI BAWAH)
$peminjaman_aktif = $mysqli->query("
    SELECT p.*, b.title, b.barcode_buku 
    FROM peminjaman p 
    JOIN books b ON p.id_buku = b.id 
    WHERE p.status = 'Dipinjam' 
    ORDER BY p.tanggal_pinjam ASC
");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Sirkulasi Pengembalian - Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <style>
        .modern-card { border: none; border-radius: 16px; box-shadow: 0 10px 30px rgba(0,0,0,0.03); background: #ffffff; }
        .form-control-barcode { border-radius: 12px; padding: 12px 15px; border: 2px solid #e2e8f0; background-color: #f8f9fa; font-weight: bold; }
        .form-control-barcode:focus { border-color: #B84E32; background-color: #fff; box-shadow: 0 0 0 0.25rem rgba(184, 78, 50, 0.1); }
        .modern-table th { text-transform: uppercase; font-size: 0.75rem; letter-spacing: 1px; color: #a0aec0; border-bottom: 2px solid #f0f2f5; padding-bottom: 15px; }
        .modern-table td { padding: 15px 10px; border-bottom: 1px solid #f0f2f5; color: #4a5568; }
        .btn-modern { border-radius: 50rem; transition: all 0.3s ease; }
        .btn-modern:hover { transform: translateY(-2px); }
        .avatar-initial { width: 40px; height: 40px; border-radius: 12px; object-fit: cover; }
        .soft-badge { padding: 6px 12px; border-radius: 8px; font-size: 0.85rem; font-weight: 600; }
    </style>
</head>
<body>

    <?php include 'sidebar.php'; ?>

    <div class="main-content" style="margin-left: 260px; padding: 30px;">
        <div class="container-fluid pb-5">
            
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h3 class="fw-bold m-0 text-dark">Sirkulasi Pengembalian</h3>
                    <p class="text-muted m-0 mt-1 fs-6">Validasi KTS Siswa & Barcode Buku untuk kalkulasi denda otomatis</p>
                </div>
                <div class="bg-white px-4 py-2 rounded-pill shadow-sm border text-primary fw-bold">
                    <i class="bi bi-shield-check me-2"></i> Mode Verifikasi Aktif
                </div>
            </div>

            <?php if(isset($_GET['pesan']) && $_GET['pesan'] == 'sukses'): ?>
                <div class="alert alert-success border-0 bg-success bg-opacity-10 text-success fw-bold rounded-4 p-3 mb-4 shadow-sm">
                    <i class="bi bi-check-circle-fill me-2 fs-5 align-middle"></i> <span class="align-middle">Buku berhasil dikembalikan dan stok diperbarui!</span>
                </div>
            <?php endif; ?>

            <div class="modern-card p-4 p-lg-5 mb-5 border-start border-primary border-4">
                <form id="formSirkulasiKembali" onsubmit="return false;" class="row g-4">
                    <div class="col-md-6">
                        <label class="fw-bold small mb-2 text-primary">1. SCAN KTS SISWA (VERIFIKASI IDENTITAS)</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light"><i class="bi bi-person-vcard"></i></span>
                            <input type="text" id="scan_kts_kembali" class="form-control form-control-barcode" placeholder="Scan kartu siswa disini..." autofocus autocomplete="off">
                        </div>
                        <div id="status_siswa" class="mt-2 small fw-bold text-primary"></div>
                    </div>

                    <div class="col-md-6">
                        <label class="fw-bold small mb-2 text-success">2. SCAN BARCODE BUKU FISIK</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light"><i class="bi bi-upc-scan"></i></span>
                            <input type="text" id="scan_buku_kembali" class="form-control form-control-barcode" placeholder="Scan barcode fisik buku..." disabled autocomplete="off">
                        </div>
                        <div id="status_buku" class="mt-2 small fw-bold text-success"></div>
                    </div>
                </form>

                <div id="panel_hasil_kembali" class="mt-4 p-4 rounded-4 border d-none" style="background-color: #fcfdfc;">
                    <form method="POST">
                        <input type="hidden" name="id_peminjaman" id="id_peminjaman_final">
                        <div class="row align-items-center">
                            <div class="col-md-8">
                                <small class="text-muted text-uppercase fw-bold tracking-wider">Kesesuaian Data Terverifikasi:</small>
                                <h4 id="res_judul" class="fw-bold text-dark mt-2 mb-1">-</h4>
                                <p id="res_nama" class="text-muted mb-2">-</p>
                                <div id="res_denda_info"></div>
                            </div>
                            <div class="col-md-4 text-end">
                                <button type="submit" name="konfirmasi_kembali" class="btn btn-success btn-lg px-5 rounded-pill fw-bold shadow-sm">
                                    <i class="bi bi-check2-all me-2"></i>Konfirmasi Terima
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <div class="modern-card p-4 p-lg-5">
                <h5 class="fw-bold mb-4 text-dark"><i class="bi bi-clock-history text-warning me-2"></i> Daftar Buku Belum Kembali</h5>
                <div class="table-responsive">
                    <table class="table modern-table align-middle">
                        <thead>
                            <tr>
                                <th width="5%">No</th>
                                <th width="30%">Peminjam</th>
                                <th width="30%">Buku & Barcode</th>
                                <th width="20%">Batas Kembali</th>
                                <th width="15%" class="text-center">Status Waktu</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            $no = 1; 
                            if($peminjaman_aktif->num_rows > 0):
                            while($p = $peminjaman_aktif->fetch_assoc()): 
                                $tgl_sekarang = new DateTime();
                                $tgl_tempo = new DateTime($p['tanggal_kembali']);
                                $terlambat = $tgl_sekarang > $tgl_tempo;
                                $badge_class = $terlambat ? 'bg-danger bg-opacity-10 text-danger' : 'bg-light text-secondary border';
                            ?>
                            <tr>
                                <td class="fw-bold text-muted"><?= $no++ ?></td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <img src="https://ui-avatars.com/api/?name=<?= urlencode($p['nama_peminjam']) ?>&background=random&color=fff&bold=true" class="avatar-initial me-3 shadow-sm">
                                        <div>
                                            <h6 class="mb-0 fw-bold text-dark"><?= htmlspecialchars($p['nama_peminjam']) ?></h6>
                                            <small class="text-muted"><?= htmlspecialchars($p['kelas']) ?></small>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <span class="fw-semibold text-dark d-block"><?= htmlspecialchars($p['title']) ?></span>
                                    <code class="text-success small fw-bold"><?= $p['barcode_buku'] ?></code>
                                </td>
                                <td>
                                    <span class="soft-badge <?= $badge_class ?>">
                                        <i class="bi bi-stopwatch me-1"></i> <?= date('d M Y', strtotime($p['tanggal_kembali'])) ?>
                                    </span>
                                </td>
                                <td class="text-center">
                                    <?php if($terlambat): ?>
                                        <span class="badge bg-danger rounded-pill px-3 py-2">Terlambat</span>
                                    <?php else: ?>
                                        <span class="badge bg-success rounded-pill px-3 py-2">Aktif</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php endwhile; else: ?>
                            <tr><td colspan="5" class="text-center py-5 text-muted"><i class="bi bi-emoji-smile fs-3 d-block mb-2"></i>Semua buku sirkulasi aman di dalam rak.</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$(document).ready(function() {
    let nama_peminjam_terdeteksi = "";

    // Mencegah gangguan tombol enter otomatis bawaan scanner laser
    $('#formSirkulasiKembali').on('keydown', function(e) {
        if (e.keyCode == 13) { e.preventDefault(); return false; }
    });

    // 1. EVENT PEMINDAIAN KTS SISWA
    $('#scan_kts_kembali').on('change', function() {
        let nama_input = $(this).val().trim();
        if(nama_input == "") return;

        // TUNTAS: Panggil ambil_data_auto.php dulu untuk menerjemahkan kode "KTS..." menjadi nama asli siswa
        $.post('ambil_data_auto.php', {type: 'siswa', code: nama_input}, function(siswaRes) {
            try {
                let siswaJson = JSON.parse(siswaRes);
                
                // Jika input berupa barcode kartu baru, ubah isinya menjadi Nama Terdaftar (misal: "imamgp")
                if(siswaJson.status == 'success') {
                    nama_input = siswaJson.data.nama_siswa || siswaJson.data.nama;
                }
            } catch(e) {
                // Jika input sudah berupa nama mentah (bukan barcode), diamkan dan lanjut
            }

            // Jalankan proses validasi tanggungan pinjaman utama ke database
            $.post('check_peminjam.php', {nama: nama_input}, function(res) {
                try {
                    let json = JSON.parse(res);
                    if(json.status == 'success') {
                        nama_peminjam_terdeteksi = json.nama; // Gunakan nama terverifikasi dari DB
                        $('#status_siswa').html('<span class="text-success fw-bold"><i class="bi bi-check-circle-fill"></i> Terverifikasi Peminjam: ' + json.nama + '</span>');
                        $('#scan_kts_kembali').attr('readonly', true).addClass('bg-light');
                        $('#scan_buku_kembali').attr('disabled', false).focus();
                    } else {
                        alert(json.msg);
                        $('#scan_kts_kembali').val('').focus();
                    }
                } catch(e) {
                    alert("Data tidak ditemukan atau siswa tidak memiliki tanggungan pinjaman buku!");
                    $('#scan_kts_kembali').val('').focus();
                }
            });
        });
    });

    // 2. EVENT PEMINDAIAN BARCODE BUKU & MATCHING DATA
    $('#scan_buku_kembali').on('change', function() {
        let barcode = $(this).val().trim();
        if(barcode == "") return;

        // AJAX memvalidasi kecocokan buku dengan siswa serta menghitung denda otomatis
        $.post('match_kembali.php', {barcode: barcode, nama: nama_peminjam_terdeteksi}, function(res) {
            try {
                let json = JSON.parse(res);
                if(json.status == 'success') {
                    $('#panel_hasil_kembali').removeClass('d-none');
                    $('#id_peminjaman_final').val(json.id_pinjam);
                    $('#res_judul').text(json.judul);
                    $('#res_nama').text("Siswa: " + json.nama + " (" + json.kelas + ")");
                    
                    if(json.denda > 0) {
                        $('#res_denda_info').html('<span class="badge bg-danger fs-6 rounded-3 p-2"><i class="bi bi-exclamation-triangle-fill me-2"></i>TERLAMBAT ' + json.hari + ' HARI (DENDA: Rp ' + json.denda.toLocaleString('id-ID') + ')</span>');
                    } else {
                        $('#res_denda_info').html('<span class="badge bg-success fs-6 rounded-3 p-2"><i class="bi bi-check-circle-fill me-2"></i>TEPAT WAKTU (BEBAS DENDA)</span>');
                    }
                } else {
                    alert(json.msg);
                    $('#scan_buku_kembali').val('').focus();
                }
            } catch(e) {
                alert("Barcode buku fisik tidak cocok dengan tanggungan pinjaman siswa!");
                $('#scan_buku_kembali').val('').focus();
            }
        });
    });
});
</script>