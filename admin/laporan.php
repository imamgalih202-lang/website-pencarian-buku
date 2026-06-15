<?php
require_once 'cek_sesi.php';
require_once '../config/koneksi.php';

// 1. Ambil Parameter Filter (Default: Bulan dan Tahun aktif saat ini)
$bulan_pilihan = isset($_GET['bulan']) ? $_GET['bulan'] : date('m');
$tahun_pilihan = isset($_GET['tahun']) ? $_GET['tahun'] : date('Y');

// Pastikan format bulan selalu 2 digit (misal: 3 menjadi 03) agar cocok dengan database
$bulan_pilihan = str_pad($bulan_pilihan, 2, "0", STR_PAD_LEFT);

$nama_bulan = [
    '01' => 'Januari', '02' => 'Februari', '03' => 'Maret', '04' => 'April',
    '05' => 'Mei', '06' => 'Juni', '07' => 'Juli', '08' => 'Agustus',
    '09' => 'September', '10' => 'Oktober', '11' => 'November', '12' => 'Desember'
];

// 2. Ambil Data Transaksi Sirkulasi Sesuai Filter Bulan & Tahun yang Dipilih
$query_laporan = $mysqli->prepare("
    SELECT p.*, b.title, b.barcode_buku 
    FROM peminjaman p
    JOIN books b ON p.id_buku = b.id
    WHERE MONTH(p.tanggal_pinjam) = ? 
    AND YEAR(p.tanggal_pinjam) = ?
    ORDER BY p.tanggal_pinjam DESC, p.id DESC
");
$query_laporan->bind_param('ss', $bulan_pilihan, $tahun_pilihan);
$query_laporan->execute();
$data_sirkulasi = $query_laporan->get_result();
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Peminjaman Bulanan - Panel Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Poppins', sans-serif; background-color: #f8f9fa; }
        .main-content { margin-left: 260px; padding: 30px; }
        .modern-card { border: none; border-radius: 16px; box-shadow: 0 10px 30px rgba(0,0,0,0.03); background: #ffffff; padding: 1.5rem; }
        .form-select { border-radius: 10px; padding: 10px 15px; border: 1px solid #e2e8f0; background-color: #f8f9fa; height: 50px; font-size: 0.95rem; }
        .form-select:focus { background-color: #fff; border-color: #B84E32; box-shadow: 0 0 0 0.25rem rgba(184, 78, 50, 0.15); }
        .modern-table th { text-transform: uppercase; font-size: 0.75rem; letter-spacing: 1px; color: #a0aec0; border-bottom: 2px solid #f0f2f5; }
        .btn-filter { height: 50px; border-radius: 10px; display: flex; align-items: center; justify-content: center; font-size: 1.2rem; transition: all 0.2s; }
        .btn-filter:hover { background-color: #111827 !important; transform: scale(1.02); }
    </style>
</head>
<body>

    <?php include 'sidebar.php'; ?>

    <div class="main-content">
        <div class="container-fluid pb-5">
            
            <div class="d-flex justify-content-between align-items-center mb-4 pb-3 border-bottom">
                <div>
                    <h3 class="fw-bold m-0 text-dark">Laporan Bulanan Peminjaman</h3>
                    <p class="text-muted m-0 mt-1 fs-6">Rekapitulasi berkas data sirkulasi buku per periode</p>
                </div>
                <div class="bg-white px-4 py-2 rounded-pill shadow-sm border">
                    <span class="text-muted small fw-bold"><i class="bi bi-calendar3 text-primary me-2"></i> <?= date('d M Y') ?></span>
                </div>
            </div>

            <div class="modern-card mb-4">
                <div class="row align-items-center g-4">
                    <div class="col-md-7 border-end pe-md-4">
                        <h6 class="fw-bold text-muted mb-3"><i class="bi bi-funnel me-1"></i> Saring Periode Laporan</h6>
                        
                        <form method="GET" action="laporan.php" class="row g-2">
                            <div class="col-5">
                                <select name="bulan" class="form-select">
                                    <?php foreach($nama_bulan as $key => $val): ?>
                                        <option value="<?= $key ?>" <?= $bulan_pilihan == $key ? 'selected' : '' ?>><?= $val ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-5">
                                <select name="tahun" class="form-select">
                                    <?php for($y = date('Y'); $y >= date('Y')-3; $y--): ?>
                                        <option value="<?= $y ?>" <?= $tahun_pilihan == $y ? 'selected' : '' ?>><?= $y ?></option>
                                    <?php endfor; ?>
                                </select>
                            </div>
                            <div class="col-2">
                                <button type="submit" class="btn btn-dark w-100 btn-filter text-white fw-bold" title="Terapkan Filter">
                                    <i class="bi bi-check-lg"></i>
                                </button>
                            </div>
                        </form>
                    </div>

                    <div class="col-md-5 ps-md-4">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <h6 class="fw-bold text-dark mb-1">Cetak Lembar Fisik</h6>
                                <p class="small text-muted m-0">Format resmi SMK N 1 Kismantoro</p>
                            </div>
                            <a href="cetak_rekap.php?bulan=<?= $bulan_pilihan ?>&tahun=<?= $tahun_pilihan ?>" target="_blank" class="btn btn-warning px-4 py-2.5 rounded-pill fw-bold text-dark shadow-sm">
                                <i class="bi bi-printer-fill me-1"></i> Cetak Laporan
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <div class="modern-card p-4">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h5 class="fw-bold m-0 text-dark">
                        <i class="bi bi-collection text-warning me-2"></i>
                        Laporan Bulan: <span class="text-primary"><?= $nama_bulan[$bulan_pilihan] ?> <?= $tahun_pilihan ?></span>
                    </h5>
                    <span class="badge bg-light text-dark border px-3 py-2 rounded-pill">Total: <?= $data_sirkulasi->num_rows ?> Transaksi</span>
                </div>

                <div class="table-responsive">
                    <table class="table modern-table align-middle">
                        <thead>
                            <tr>
                                <th width="25%">Nama Peminjam</th>
                                <th width="15%">Kelas</th>
                                <th width="35%">Buku & Barcode</th>
                                <th width="15%">Tgl Pinjam</th>
                                <th width="10%">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if($data_sirkulasi->num_rows > 0): ?>
                                <?php while($row = $data_sirkulasi->fetch_assoc()): ?>
                                <tr>
                                    <td class="fw-bold text-dark"><?= htmlspecialchars($row['nama_peminjam']) ?></td>
                                    <td><span class="badge bg-light text-dark border"><?= htmlspecialchars($row['kelas']) ?></span></td>
                                    <td>
                                        <span class="d-block fw-semibold text-dark"><?= htmlspecialchars($row['title']) ?></span>
                                        <code class="text-success small fw-bold"><?= htmlspecialchars($row['barcode_buku']) ?></code>
                                    </td>
                                    <td class="small"><?= date('d M Y', strtotime($row['tanggal_pinjam'])) ?></td>
                                    <td>
                                        <span class="badge bg-<?= $row['status'] === 'Dipinjam' ? 'warning' : 'success' ?> bg-opacity-10 text-<?= $row['status'] === 'Dipinjam' ? 'warning' : 'success' ?> rounded-pill px-3 py-1.5" style="font-size: 0.75rem; font-weight: 600;">
                                            <?= $row['status'] ?>
                                        </span>
                                    </td>
                                </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="5" class="text-center py-5 text-muted">
                                        <i class="bi bi-folder-x display-5 d-block opacity-20 mb-2"></i>
                                        Tidak ada rekapan data peminjaman pada bulan ini (<?= $nama_bulan[$bulan_pilihan] ?>).
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>

</body>
</html>