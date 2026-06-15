<?php
require_once 'cek_sesi.php';
require_once '../config/koneksi.php';

// 🔥 TAMBAHKAN BARIS INI AGAR WAKTU SINKRON KE WIB (WAKTU INDONESIA BARAT)
date_default_timezone_set('Asia/Jakarta');

// 1. Ambil Parameter Filter Rekap Bulanan (Default: Bulan dan Tahun aktif saat ini)
$bulan_pilihan = isset($_GET['bulan']) ? $mysqli->real_escape_string($_GET['bulan']) : date('m');
$tahun_pilihan = isset($_GET['tahun']) ? $mysqli->real_escape_string($_GET['tahun']) : date('Y');

$nama_bulan = [
    '01' => 'Januari', '02' => 'Februari', '03' => 'Maret', '04' => 'April',
    '05' => 'Mei', '06' => 'Juni', '07' => 'Juli', '08' => 'Agustus',
    '09' => 'September', '10' => 'Oktober', '11' => 'November', '12' => 'Desember'
];

// 2. Kueri Menampilkan Seluruh Riwayat Kunjungan (Urut Berdasarkan yang Terbaru)
$sql_hari_ini = "SELECT * FROM pengunjung ORDER BY tgl_kunjung DESC";
$query_pengunjung = $mysqli->query($sql_hari_ini);

if (!$query_pengunjung) {
    die("Ada masalah pada Database: " . $mysqli->error);
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Pengunjung - Panel Admin</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <style>
        body { font-family: 'Poppins', sans-serif; background-color: #f8f9fa; }
        .main-content { margin-left: 260px; padding: 30px; }
        .modern-card { border: none; border-radius: 16px; box-shadow: 0 10px 30px rgba(0,0,0,0.03); background: #ffffff; padding: 1.5rem; }
        .form-select { border-radius: 10px; padding: 10px 15px; border: 1px solid #e2e8f0; background-color: #f8f9fa; }
        .form-select:focus { background-color: #fff; border-color: #B84E32; box-shadow: 0 0 0 0.25rem rgba(184, 78, 50, 0.15); }
    </style>

    <?php include 'sidebar.php'; ?>
</head>
<body>

<div class="main-content">
    <div class="container-fluid pb-5">
        
        <div class="d-flex justify-content-between align-items-center mb-4 pb-3 border-bottom">
            <div>
                <h3 class="fw-bold m-0 text-dark">Daftar Kehadiran Siswa</h3>
                <p class="text-muted m-0 mt-1 fs-6">Siswa yang melakukan scan KTS di pintu masuk & sirkulasi otomatis</p>
            </div>
            <div class="bg-white px-4 py-2 rounded-pill shadow-sm border">
                <span class="text-muted small fw-bold">
                    <i class="bi bi-people-fill text-primary me-2"></i> 
                    Total Log Kunjungan: <?= $query_pengunjung->num_rows ?> Siswa
                </span>
            </div>
        </div>

        <div class="modern-card mb-4">
            <div class="row align-items-center g-4">
                <div class="col-md-7 border-end pe-md-4">
                    <h6 class="fw-bold text-muted mb-2"><i class="bi bi-calendar-check me-1"></i> Rekap Kunjungan Bulanan</h6>
                    <p class="small text-muted m-0">Silakan pilih bulan dan tahun untuk mengunduh berkas laporan cetak atau pdf kehadiran siswa.</p>
                </div>

                <div class="col-md-5 ps-md-4">
                    <form method="GET" action="cetak_pengunjung.php" target="_blank" class="row g-2">
                        <div class="col-5">
                            <select name="bulan" class="form-select form-select-sm">
                                <?php foreach($nama_bulan as $key => $val): ?>
                                    <option value="<?= $key ?>" <?= $bulan_pilihan == $key ? 'selected' : '' ?>><?= $val ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-4">
                            <select name="tahun" class="form-select form-select-sm">
                                <?php for($y = date('Y'); $y >= date('Y')-3; $y--): ?>
                                    <option value="<?= $y ?>" <?= $tahun_pilihan == $y ? 'selected' : '' ?>><?= $y ?></option>
                                <?php endfor; ?>
                            </select>
                        </div>
                        <div class="col-3">
                            <button type="submit" class="btn btn-warning btn-sm w-100 h-100 fw-bold rounded-3 text-dark" title="Cetak Rekap">
                                <i class="bi bi-printer-fill me-1"></i> Cetak
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="card shadow-sm border-0" style="border-radius: 15px; overflow: hidden;">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th class="py-3 px-4 text-secondary small fw-bold" style="letter-spacing: 1px;">WAKTU MASUK</th>
                                <th class="py-3 text-secondary small fw-bold" style="letter-spacing: 1px;">IDENTITAS SISWA</th>
                                <th class="py-3 text-secondary small fw-bold" style="letter-spacing: 1px;">KELAS / JURUSAN</th>
                                <th class="py-3 text-center text-secondary small fw-bold" style="letter-spacing: 1px;">STATUS</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if($query_pengunjung->num_rows > 0): ?>
                                <?php while($row = $query_pengunjung->fetch_assoc()): 
                                    $nis_scan = isset($row['nis']) ? $row['nis'] : '-';
                                    
                                    // SINKRONISASI DATABASENYA: Prioritaskan membaca kolom nama_pengunjung dari data baru
                                    if (!empty($row['nama_pengunjung'])) {
                                        $nama_tampil = $row['nama_pengunjung'];
                                        
                                        // Cari data kelasnya ke tabel siswa berdasarkan string barcode kartu
                                        $cari_kelas = $mysqli->query("SELECT kelas FROM siswa WHERE barcode_siswa = '$nis_scan' OR nis = '$nis_scan' LIMIT 1");
                                        $data_k = $cari_kelas->fetch_assoc();
                                        $kelas_tampil = $data_k['kelas'] ?? '-';
                                    } else {
                                        // BACK-UP LOGIKA: Jika records lama bernilai NULL, lacak manual ke tabel master siswa
                                        $cari_siswa = $mysqli->query("SELECT nama_siswa, kelas FROM siswa WHERE barcode_siswa = '$nis_scan' OR nis = '$nis_scan' LIMIT 1");
                                        if($cari_siswa && $cari_siswa->num_rows > 0) {
                                            $data_s = $cari_siswa->fetch_assoc();
                                            $nama_tampil = $data_s['nama_siswa'];
                                            $kelas_tampil = $data_s['kelas'];
                                        } else {
                                            $nama_tampil = 'Siswa Tanpa Nama';
                                            $kelas_tampil = '-';
                                        }
                                    }
                                ?>
                                <tr>
                                    <td class="px-4">
                                        <div class="fw-bold text-dark"><?= date('H:i', strtotime($row['tgl_kunjung'])) ?> WIB</div>
                                        <div class="text-muted small"><?= date('d M Y', strtotime($row['tgl_kunjung'])) ?></div>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <img src="https://ui-avatars.com/api/?name=<?= urlencode($nama_tampil) ?>&background=random&color=fff&bold=true" class="rounded-circle me-3" width="40" height="40">
                                            <div>
                                                <div class="fw-bold text-dark"><?= htmlspecialchars($nama_tampil) ?></div>
                                                <code class="text-primary small"><?= htmlspecialchars($nis_scan) ?></code>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge bg-light text-dark border px-3 py-2 rounded-pill fw-normal">
                                            <?= htmlspecialchars($kelas_tampil) ?>
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 px-3 py-2 rounded-pill">
                                            <i class="bi bi-check2-circle me-1"></i> Terverifikasi
                                        </span>
                                    </td>
                                </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="4" class="text-center py-5">
                                        <i class="bi bi-person-x display-1 text-muted opacity-25 d-block mb-3"></i>
                                        <h6 class="text-muted m-0">Belum ada siswa yang masuk hari ini.</h6>
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>