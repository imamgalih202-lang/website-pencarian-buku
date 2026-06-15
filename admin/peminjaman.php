<?php
require_once 'cek_sesi.php';
require_once '../config/koneksi.php';

// 🔥 TAMBAHKAN BARIS INI AGAR WAKTU SINKRON KE WIB (WAKTU INDONESIA BARAT)
date_default_timezone_set('Asia/Jakarta');

// Proses Simpan Peminjaman (Dijalankan setelah scan KTS & Buku lengkap)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['tambah_pinjam'])) {
    $nis = $mysqli->real_escape_string(trim($_POST['nis'])); 
    $barcode_buku = $mysqli->real_escape_string(trim($_POST['barcode_buku'])); 
    
    // Menangkap input string durasi yang dipilih (contoh nilai: "1_m", "3_d", "7_d", "30_d")
    $durasi_pilihan = isset($_POST['durasi_pilihan']) ? $_POST['durasi_pilihan'] : '7_d'; 
    
    // Ambil ID Buku berdasarkan barcode
    $buku_res = $mysqli->query("SELECT id FROM books WHERE barcode_buku = '$barcode_buku'");
    $buku_data = $buku_res->fetch_assoc();
    
    // AMAN & FLEKSIBEL: Mampu mendeteksi relasi siswa baik menggunakan barcode_siswa baru maupun data lama
    $siswa_res = $mysqli->query("SELECT nama_siswa, kelas FROM siswa WHERE barcode_siswa = '$nis' OR nis = '$nis' LIMIT 1");
    $siswa_data = $siswa_res->fetch_assoc();

    if ($buku_data && $siswa_data) {
        $id_buku = $buku_data['id'];
        $nama_peminjam = $siswa_data['nama_siswa']; 
        $kelas = $siswa_data['kelas'];
        
        // Simpan waktu pinjam lengkap dengan tanggal, jam, menit, dan detik berjalan
        $waktu_sekarang = date('Y-m-d H:i:s'); 
        $tgl_pinjam = $waktu_sekarang; 
        
        // Memisahkan kalkulasi batas kembali antara satuan menit (simulasi) dan hari (normal)
        if ($durasi_pilihan === '1_m') {
            $tgl_kembali = date('Y-m-d H:i:s', strtotime('+1 minutes')); 
        } elseif ($durasi_pilihan === '3_d') {
            $tgl_kembali = date('Y-m-d H:i:s', strtotime('+3 days'));
        } elseif ($durasi_pilihan === '30_d') {
            $tgl_kembali = date('Y-m-d H:i:s', strtotime('+30 days'));
        } else {
            $tgl_kembali = date('Y-m-d H:i:s', strtotime('+7 days'));
        }
        
        $status = 'Dipinjam';

        // 1. Eksekusi simpan transaksi peminjaman utama
        $stmt = $mysqli->prepare("INSERT INTO peminjaman (id_buku, nama_peminjam, kelas, tanggal_pinjam, tanggal_kembali, status) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param('isssss', $id_buku, $nama_peminjam, $kelas, $tgl_pinjam, $tgl_kembali, $status);
        $stmt->execute();
        
        // =====================================================================
        // FITUR PENGUNJUNG OTOMATIS: MENYIMPAN NIS, NAMA SISWA, DAN WAKTU KUNJUNG
        // =====================================================================
        $query_pengunjung = "INSERT INTO pengunjung (nis, nama_pengunjung, tgl_kunjung) VALUES ('$nis', '$nama_peminjam', '$waktu_sekarang')";
        $mysqli->query($query_pengunjung);
        // =====================================================================
        
        header('Location: peminjaman.php?pesan=sukses');
    } else {
        header('Location: peminjaman.php?pesan=gagal');
    }
    exit;
}

// Ambil data peminjaman aktif untuk tabel di bawah
$peminjaman_aktif = $mysqli->query("
    SELECT p.*, b.title, b.barcode_buku 
    FROM peminjaman p 
    JOIN books b ON p.id_buku = b.id 
    WHERE p.status = 'Dipinjam' 
    ORDER BY p.tanggal_pinjam DESC
");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Sirkulasi Peminjaman - Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <style>
        body { background-color: #f8f9fa; }
        .modern-card { border: none; border-radius: 16px; box-shadow: 0 10px 30px rgba(0,0,0,0.03); background: #ffffff; }
        .form-control, .form-select { border-radius: 10px; padding: 12px 15px; border: 1px solid #e2e8f0; background-color: #f8f9fa; }
        .form-control:focus, .form-select:focus { background-color: #fff; border-color: #B84E32; box-shadow: 0 0 0 0.25rem rgba(184, 78, 50, 0.15); }
        .modern-table th { text-transform: uppercase; font-size: 0.75rem; letter-spacing: 1px; color: #a0aec0; border-bottom: 2px solid #f0f2f5; }
        .status-scan { font-size: 0.85rem; font-weight: 600; margin-top: 5px; }
        .countdown-text { font-family: monospace; font-weight: bold; }
    </style>
</head>
<body>

    <?php include 'sidebar.php'; ?>

    <div class="main-content" style="margin-left: 260px; padding: 30px;">
        <div class="container-fluid pb-5">
            
            <div class="d-flex justify-content-between align-items-center mb-4 pb-3 border-bottom">
                <div>
                    <h3 class="fw-bold m-0 text-dark">Sirkulasi Otomatis (Barcode)</h3>
                    <p class="text-muted m-0 mt-1 fs-6">Scan KTS Siswa dan Barcode Buku untuk transaksi cepat</p>
                </div>
                <div class="bg-white px-4 py-2 rounded-pill shadow-sm border">
                    <span class="text-muted small fw-bold"><i class="bi bi-clock-history text-primary me-2"></i> <?= date('d M Y') ?></span>
                </div>
            </div>

            <?php if(isset($_GET['pesan']) && $_GET['pesan'] == 'sukses'): ?>
                <div class="alert alert-success border-0 shadow-sm rounded-4 mb-4"><i class="bi bi-check-circle-fill me-2"></i> Transaksi peminjaman berhasil disimpan! Data otomatis masuk log pengunjung.</div>
            <?php elseif(isset($_GET['pesan']) && $_GET['pesan'] == 'gagal'): ?>
                <div class="alert alert-danger border-0 shadow-sm rounded-4 mb-4"><i class="bi bi-exclamation-triangle-fill me-2"></i> Transaksi gagal! Data siswa atau barcode buku tidak valid.</div>
            <?php endif; ?>

            <div class="modern-card p-4 p-lg-5 mb-5">
                <form id="formSirkulasi" method="post">
                    <div class="row g-4">
                        <div class="col-md-4">
                            <label class="form-label small fw-bold text-muted mb-1">SCAN KTS SISWA</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-person-vcard"></i></span>
                                <input type="text" name="nis" id="scan_kts" class="form-control" placeholder="Arahkan scanner ke KTS..." autofocus autocomplete="off" required>
                            </div>
                            <div id="info_siswa" class="status-scan"></div>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label small fw-bold text-muted mb-1">NAMA / KELAS (OTOMATIS)</label>
                            <input type="text" id="tampil_nama" class="form-control bg-light" readonly placeholder="-">
                        </div>

                        <div class="col-md-4">
                            <label class="form-label small fw-bold text-muted mb-1">SCAN BARCODE BUKU</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-upc-scan"></i></span>
                                <input type="text" name="barcode_buku" id="scan_buku" class="form-control" placeholder="Arahkan scanner ke Buku..." autocomplete="off" required>
                            </div>
                            <div id="info_buku" class="status-scan"></div>
                        </div>
                    </div>

                    <div class="row mt-4 pt-4 border-top">
                        <div class="col-12 d-flex justify-content-end align-items-center gap-3">
                            <div class="d-flex align-items-center gap-2">
                                <label class="small fw-bold text-muted text-nowrap m-0"><i class="bi bi-calendar-range text-primary"></i> DURASI PINJAM :</label>
                                <select name="durasi_pilihan" class="form-select form-select-sm" style="width: 180px; height: 46px;">
                                    <option value="1_m" selected>1 Menit (Simulasi)</option>
                                    <option value="3_d">3 Hari</option>
                                    <option value="7_d">7 Hari (Normal)</option>
                                    <option value="30_d">30 Hari (1 Bulan)</option>
                                </select>
                            </div>
                            <button type="submit" name="tambah_pinjam" class="btn btn-dark px-5 py-2.5 rounded-pill shadow-sm fw-bold" style="height: 46px;">
                                <i class="bi bi-send-check me-2"></i> Proses Peminjaman
                            </button>
                        </div>
                    </div>
                </form>
            </div>

            <div class="modern-card p-4">
                <h5 class="fw-bold mb-4 text-dark"><i class="bi bi-layers me-2"></i> Daftar Buku Keluar</h5>
                <div class="table-responsive">
                    <table class="table modern-table">
                        <thead>
                            <tr>
                                <th>Siswa</th>
                                <th>Judul Buku</th>
                                <th>Barcode</th>
                                <th>Batas Kembali</th>
                                <th>Sisa Waktu</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if($peminjaman_aktif->num_rows > 0): ?>
                                <?php while($p = $peminjaman_aktif->fetch_assoc()): ?>
                                <tr>
                                    <td>
                                        <span class="fw-bold d-block text-dark"><?= htmlspecialchars($p['nama_peminjam']) ?></span>
                                        <small class="text-muted"><?= htmlspecialchars($p['kelas']) ?></small>
                                    </td>
                                    <td><?= htmlspecialchars($p['title']) ?></td>
                                    <td><code class="text-primary fw-bold"><?= htmlspecialchars($p['barcode_buku']) ?></code></td>
                                    <td>
                                        <span class="badge bg-light text-dark border small">
                                            <i class="bi bi-calendar3 me-1"></i> <?= date('d/m/y - H:i:s', strtotime($p['tanggal_kembali'])) ?>
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 rounded-pill px-3 py-1.5 countdown-item" 
                                              data-countdown="<?= date('c', strtotime($p['tanggal_kembali'])) ?>">
                                            <i class="bi bi-hourglass-split me-1 animate-spin"></i>
                                            <span class="countdown-text">Menghitung...</span>
                                        </span>
                                    </td>
                                    <td><a href="pengembalian.php" class="btn btn-sm btn-outline-success rounded-pill px-3">Kembalikan</a></td>
                                </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="6" class="text-center py-4 text-muted">Belum ada sirkulasi buku dipinjam hari ini.</td>
                                </tr>
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
    $('#scan_kts').focus();

    $('#formSirkulasi').on('keydown', function(e) {
        if (e.keyCode == 13) {
            e.preventDefault();
            return false;
        }
    });

    // 1. Logika Pembacaan Scan KTS Siswa via AJAX
    $('#scan_kts').on('change', function() {
        let nis = $(this).val().trim();
        if(nis != "") {
            $.post('ambil_data_auto.php', {type: 'siswa', code: nis}, function(res) {
                try {
                    let json = JSON.parse(res);
                    if(json.status == 'success') {
                        let namaSiswa = json.data.nama_siswa || json.data.nama || "Nama Tidak Terbaca";
                        let kelasSiswa = json.data.kelas || "-";

                        $('#tampil_nama').val(namaSiswa + " (" + kelasSiswa + ")");
                        $('#info_siswa').html('<span class="text-success fw-bold"><i class="bi bi-check-circle-fill"></i> Siswa Terverifikasi</span>');
                        
                        $('#scan_buku').focus(); 
                    } else {
                        alert(json.msg);
                        $('#scan_kts').val("").focus();
                        $('#tampil_nama').val("-");
                        $('#info_siswa').html("");
                    }
                } catch (e) {
                    console.error("Gagal parse JSON siswa:", res);
                }
            });
        }
    });

    // 2. Logika Pembacaan Scan Barcode Buku via AJAX
    $('#scan_buku').on('change', function() {
        let barcode = $(this).val().trim();
        if(barcode != "") {
            $.post('ambil_data_auto.php', {type: 'buku', code: barcode}, function(res) {
                try {
                    let json = JSON.parse(res);
                    if(json.status == 'success') {
                        $('#info_buku').html('<span class="text-success fw-bold"><i class="bi bi-book-half"></i> ' + json.data.title + '</span>');
                    } else {
                        alert(json.msg);
                        $('#scan_buku').val("").focus();
                        $('#info_buku').html("");
                    }
                } catch (e) {
                    console.error("Gagal parse JSON buku:", res);
                }
            });
        }
    });

    function updateCountdowns() {
        $('.countdown-item').each(function() {
            let targetDateStr = $(this).data('countdown');
            if (!targetDateStr) return;

            let targetTime = new Date(targetDateStr).getTime();
            let now = new Date().getTime();
            let difference = targetTime - now;

            let textSpan = $(this).find('.countdown-text');

            if (difference <= 0) {
                $(this).removeClass('bg-success text-success border-success')
                       .addClass('bg-danger text-danger border-danger bg-opacity-10');
                $(this).find('i').removeClass('bi-hourglass-split').addClass('bi-exclamation-circle-fill');
                textSpan.text('Waktu Habis / Terlambat');
            } else {
                let days = Math.floor(difference / (1000 * 60 * 60 * 24));
                let hours = Math.floor((difference % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
                let minutes = Math.floor((difference % (1000 * 60 * 60)) / (1000 * 60));
                let seconds = Math.floor((difference % (1000 * 60)) / 1000);

                let displayStr = "";
                if (days > 0) {
                    displayStr += days + "h " + hours + "j " + minutes + "m " + seconds + "d";
                } else if (hours > 0) {
                    displayStr += hours + "j " + minutes + "m " + seconds + "d";
                } else {
                    displayStr += minutes + "m " + seconds + "d";
                }

                textSpan.text(displayStr);
            }
        });
    }

    updateCountdowns();
    setInterval(updateCountdowns, 1000);
});
</script>
</body>
</html>