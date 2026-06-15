<?php
// Wajib panggil satpam dulu di baris paling atas
require_once 'cek_sesi.php';
// Panggil koneksi database
require_once '../config/koneksi.php';

// 1. Ambil Statistik Cerdas dari Database
$total_buku = $mysqli->query("SELECT COUNT(*) as total FROM books")->fetch_assoc()['total'];
$sedang_dipinjam = $mysqli->query("SELECT COUNT(*) as total FROM peminjaman WHERE status = 'Dipinjam'")->fetch_assoc()['total'];
$total_siswa = $mysqli->query("SELECT COUNT(*) as total FROM siswa")->fetch_assoc()['total'];

// 2. LOGIKA GRAFIK BULANAN (Sesuai format SMK N 1 Kismantoro):
// Mengambil total peminjaman per tanggal untuk bulan berjalan (1 sampai 30/31)
$bulan_sekarang = date('m');
$tahun_sekarang = date('Y');
$jumlah_hari = cal_days_in_month(CAL_GREGORIAN, $bulan_sekarang, $tahun_sekarang);

// Siapkan wadah array tanggal default 1-31 diisi angka 0
$label_grafik = [];
$data_grafik = [];
for ($d = 1; $d <= $jumlah_hari; $d++) {
    $label_grafik[] = $d;
    $data_grafik[$d] = 0; // Set default jika hari tersebut tidak ada transaksi
}

// Ambil data peminjaman aktual dari database untuk bulan ini
$query_peminjaman_bulanan = $mysqli->query("
    SELECT DAY(tanggal_pinjam) as tanggal, COUNT(*) as jumlah 
    FROM peminjaman 
    WHERE MONTH(tanggal_pinjam) = '$bulan_sekarang' 
    AND YEAR(tanggal_pinjam) = '$tahun_sekarang'
    GROUP BY DAY(tanggal_pinjam)
");

while($row = $query_peminjaman_bulanan->fetch_assoc()) {
    $tgl = (int)$row['tanggal'];
    $data_grafik[$tgl] = (int)$row['jumlah'];
}

// Ubah kembali array asosiatif menjadi array indeks biasa untuk Chart.js
$data_grafik_final = array_values($data_grafik);
$nama_bulan_ini = date('F Y');

// 3. Ambil 5 Aktivitas Peminjaman Terbaru
$peminjaman_terbaru = $mysqli->query("
    SELECT p.*, b.title 
    FROM peminjaman p 
    JOIN books b ON p.id_buku = b.id 
    ORDER BY p.tanggal_pinjam DESC, p.id DESC 
    LIMIT 5
");

$nama_admin = $_SESSION['username'] ?? 'Admin';
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin - Perpustakaan</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Poppins', sans-serif; background-color: #f8f9fa; }
        .main-content { margin-left: 260px; padding: 30px; }
        .modern-card {
            border: none;
            border-radius: 20px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.03);
            background: #ffffff;
            padding: 1.5rem;
        }
        .icon-box {
            width: 55px; height: 55px;
            border-radius: 15px;
            display: flex; align-items: center; justify-content: center;
        }
        .modern-table th {
            text-transform: uppercase; font-size: 0.7rem; letter-spacing: 1px; color: #a0aec0;
            border-bottom: 2px solid #f0f2f5;
        }
        .shortcut-btn {
            border-radius: 15px; padding: 15px; text-align: center; text-decoration: none;
            color: #4a5568; background: #fff; border: 1px solid #e2e8f0; transition: all 0.2s;
            display: block;
        }
        .shortcut-btn:hover { border-color: #B84E32; color: #B84E32; background: #fff5f2; transform: translateY(-3px); }
    </style>
</head>
<body>

    <?php include 'sidebar.php'; ?>

    <div class="main-content">
        <div class="container-fluid">
            
            <div class="d-flex justify-content-between align-items-center mb-5">
                <div>
                    <h3 class="fw-bold m-0 text-dark">Dashboard Statistik</h3>
                    <p class="m-0 text-muted mt-1 small">Analisis data kunjungan dan koleksi perpustakaan</p>
                </div>
                <div class="d-flex align-items-center bg-white px-3 py-2 rounded-pill shadow-sm border">
                    <img src="https://ui-avatars.com/api/?name=<?= urlencode($nama_admin) ?>&background=B84E32&color=fff" class="rounded-circle me-2" width="35">
                    <span class="fw-bold small text-dark"><?= htmlspecialchars($nama_admin) ?></span>
                </div>
            </div>

            <div class="row g-4 mb-4">
                <div class="col-md-4">
                    <div class="modern-card d-flex align-items-center justify-content-between">
                        <div>
                            <p class="text-muted fw-bold small mb-1">TOTAL BUKU</p>
                            <h2 class="fw-bold mb-0"><?= $total_buku ?></h2>
                        </div>
                        <div class="icon-box bg-primary bg-opacity-10 text-primary"><i class="bi bi-book fs-3"></i></div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="modern-card d-flex align-items-center justify-content-between">
                        <div>
                            <p class="text-muted fw-bold small mb-1">DIPINJAM</p>
                            <h2 class="fw-bold mb-0"><?= $sedang_dipinjam ?></h2>
                        </div>
                        <div class="icon-box bg-warning bg-opacity-10 text-warning"><i class="bi bi-clock-history fs-3"></i></div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="modern-card d-flex align-items-center justify-content-between">
                        <div>
                            <p class="text-muted fw-bold small mb-1">ANGGOTA</p>
                            <h2 class="fw-bold mb-0"><?= $total_siswa ?></h2>
                        </div>
                        <div class="icon-box bg-success bg-opacity-10 text-success"><i class="bi bi-people fs-3"></i></div>
                    </div>
                </div>
            </div>

            <div class="row g-4">
                <div class="col-lg-8">
                    <div class="modern-card mb-4">
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <h5 class="fw-bold m-0"><i class="bi bi-bar-chart-fill text-danger me-2"></i>Grafik Data Peminjaman Buku</h5>
                            <span class="badge bg-light text-dark border"><?= $nama_bulan_ini ?></span>
                        </div>
                        <canvas id="chartPeminjamanBulanan" height="150"></canvas>
                    </div>

                    <div class="modern-card">
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <h6 class="fw-bold m-0">Peminjaman Terbaru</h6>
                            <a href="peminjaman.php" class="text-primary small text-decoration-none fw-bold">Lihat Semua</a>
                        </div>
                        <div class="table-responsive">
                            <table class="table modern-table align-middle">
                                <thead>
                                    <tr>
                                        <th>Siswa</th>
                                        <th>Buku</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if($peminjaman_terbaru->num_rows > 0): while($pt = $peminjaman_terbaru->fetch_assoc()): ?>
                                    <tr>
                                        <td class="fw-bold"><?= htmlspecialchars($pt['nama_peminjam']) ?></td>
                                        <td class="small text-muted"><?= htmlspecialchars(substr($pt['title'], 0, 35)) ?>...</td>
                                        <td>
                                            <span class="badge bg-<?= $pt['status'] == 'Dipinjam' ? 'warning' : 'success' ?> bg-opacity-10 text-<?= $pt['status'] == 'Dipinjam' ? 'warning' : 'success' ?> px-2 py-1 rounded-pill" style="font-size: 0.7rem;">
                                                <?= $pt['status'] ?>
                                            </span>
                                        </td>
                                    </tr>
                                    <?php endwhile; else: ?>
                                    <tr><td colspan="3" class="text-center py-4 small text-muted">Belum ada aktivitas.</td></tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4">
                    <div class="modern-card">
                        <h6 class="fw-bold mb-4">Akses Cepat</h6>
                        <div class="row g-3">
                            <div class="col-6"><a href="peminjaman.php" class="shortcut-btn shadow-sm"><i class="bi bi-upc-scan d-block mb-1"></i><small class="fw-bold">Scan</small></a></div>
                            <div class="col-6"><a href="pengembalian.php" class="shortcut-btn shadow-sm"><i class="bi bi-arrow-return-left d-block mb-1"></i><small class="fw-bold">Balik</small></a></div>
                            <div class="col-6"><a href="books.php" class="shortcut-btn shadow-sm"><i class="bi bi-plus-circle d-block mb-1"></i><small class="fw-bold">Buku</small></a></div>
                            <div class="col-6"><a href="cetak_kartu.php" class="shortcut-btn shadow-sm"><i class="bi bi-card-image d-block mb-1"></i><small class="fw-bold">KTS</small></a></div>
                        </div>

                        <div class="mt-4 p-3 rounded-4 bg-dark text-white shadow-sm position-relative overflow-hidden">
                            <h6 class="fw-bold mb-1 position-relative z-1">Rekap Bulanan</h6>
                            <p class="small opacity-75 mb-3 position-relative z-1">Unduh lembar rekap fisik (PDF)</p>
                            <a href="cetak_rekap.php" target="_blank" class="btn btn-warning btn-sm fw-bold rounded-pill px-4 position-relative z-1">
                                <i class="bi bi-printer-fill me-1"></i> Cetak Rekap
                            </a>
                            <i class="bi bi-file-earmark-bar-graph position-absolute opacity-25" style="font-size: 4rem; right: -5px; bottom: -15px;"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        const ctx = document.getElementById('chartPeminjamanBulanan').getContext('2d');
        
        // Membuat variasi palet warna batang dinamis (teal/merah muda) menyerupai kertas fisik laporan
        const labelDays = <?= json_encode($label_grafik); ?>;
        const dataValues = <?= json_encode($data_grafik_final); ?>;
        
        const backgroundColors = dataValues.map((val, index) => {
            return index % 2 === 0 ? '#319795' : '#E53E3E'; // Kombinasi warna batang estetik
        });

        new Chart(ctx, {
            type: 'bar', // Mengubah tipe grafik dari 'line' menjadi 'bar' (Grafik Batang)
            data: {
                labels: labelDays,
                datasets: [{
                    label: 'Jumlah Buku Dipinjam',
                    data: dataValues,
                    backgroundColor: backgroundColors,
                    borderRadius: 5,
                    borderWidth: 0
                }]
            },
            options: {
                responsive: true,
                plugins: { 
                    legend: { display: false }
                },
                scales: { 
                    y: { 
                        beginAtZero: true, 
                        ticks: { stepSize: 1, color: '#a0aec0' }, 
                        grid: { color: '#f0f2f5' } 
                    },
                    x: { 
                        ticks: { color: '#a0aec0', font: { size: 10 } }, 
                        grid: { display: false } 
                    }
                }
            }
        });
    </script>
</body>
</html>