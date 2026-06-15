<?php
require_once 'cek_sesi.php';
require_once '../config/koneksi.php';

// Ambil parameter bulan dan tahun dari URL jika ada, jika tidak ada gunakan bulan & tahun saat ini
$bulan = isset($_GET['bulan']) ? $mysqli->real_escape_string($_GET['bulan']) : date('m');
$tahun = isset($_GET['tahun']) ? $mysqli->real_escape_string($_GET['tahun']) : date('Y');

// Array nama bulan dalam bahasa Indonesia untuk kop laporan
$nama_bulan = [
    '01' => 'Januari', '02' => 'Februari', '03' => 'Maret', '04' => 'April',
    '05' => 'Mei', '06' => 'Juni', '07' => 'Juli', '08' => 'Agustus',
    '09' => 'September', '10' => 'Oktober', '11' => 'November', '12' => 'Desember'
];

$bulan_tampil = $nama_bulan[$bulan];
$jumlah_hari = cal_days_in_month(CAL_GREGORIAN, $bulan, $tahun);

// 1. Siapkan struktur data tanggal 1 sampai 31
$data_rekap = [];
for ($i = 1; $i <= 31; $i++) {
    $data_rekap[$i] = 0; // Default diisi 0 jika di luar jumlah hari atau tidak ada transaksi
}

// 2. Tarik data peminjaman aktual berdasarkan rentang waktu bulan terpilih
$query_aktual = $mysqli->query("
    SELECT DAY(tanggal_pinjam) as tgl, COUNT(*) as jumlah 
    FROM peminjaman 
    WHERE MONTH(tanggal_pinjam) = '$bulan' 
    AND YEAR(tanggal_pinjam) = '$tahun'
    GROUP BY DAY(tanggal_pinjam)
");

$total_seluruh_peminjaman = 0;
while ($row = $query_aktual->fetch_assoc()) {
    $tgl_index = (int)$row['tgl'];
    $data_rekap[$tgl_index] = (int)$row['jumlah'];
    $total_seluruh_peminjaman += (int)$row['jumlah'];
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Cetak Laporan Bulanan Peminjaman Buku</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            color: #000;
            background: #fff;
            margin: 0;
            padding: 20px;
            font-size: 12px;
        }
        .container {
            width: 100%;
            max-width: 800px;
            margin: 0 auto;
        }
        .header-laporan {
            text-align: center;
            margin-bottom: 25px;
            line-height: 1.4;
        }
        .header-laporan h2 {
            margin: 0;
            font-size: 16px;
            font-weight: bold;
            letter-spacing: 0.5px;
        }
        .header-laporan h3 {
            margin: 5px 0 0 0;
            font-size: 14px;
            font-weight: bold;
        }
        
        /* Layout Pembagian Kolom Kertas Asli */
        .content-layout {
            display: flex;
            justify-content: space-between;
            margin-bottom: 30px;
            gap: 20px;
        }
        .table-area {
            width: 32%;
        }
        .chart-area {
            width: 65%;
            border: 1px solid #000;
            padding: 15px;
            box-sizing: border-box;
            position: relative;
        }
        .chart-title {
            text-align: center;
            font-weight: bold;
            font-size: 11px;
            margin-bottom: 15px;
        }

        /* Tabel Data Format Fisik Instansi */
        table.rekap-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 11px;
        }
        table.rekap-table th, table.rekap-table td {
            border: 1px solid #000;
            padding: 4px 6px;
            text-align: center;
        }
        table.rekap-table th {
            background-color: #f2f2f2;
            font-weight: bold;
        }
        .text-left { text-align: left !important; }
        .fw-bold { font-weight: bold; }

        /* Tanda Tangan Lapisan Bawah Kertas */
        .ttd-layout {
            display: flex;
            justify-content: space-between;
            margin-top: 40px;
            padding: 0 10px;
            font-size: 12px;
        }
        .ttd-box {
            width: 40%;
            text-align: center;
            line-height: 1.3;
        }
        .ttd-space {
            height: 70px;
        }

        /* Aturan Khusus Saat Tombol Print Dipicu */
        @media print {
            body { padding: 0; margin: 0; }
            .container { width: 100%; max-width: 100%; }
            @page { size: A4 portrait; margin: 1.5cm; }
        }
    </style>
</head>
<body>

<div class="container">
    
    <div class="header-laporan">
        <h2>PERPUSTAKAAN SMK NEGERI 1 KISMANTORO</h2>
        <h2>DATA PEMINJAMAN BUKU PERPUSTAKAAN</h2>
        <h2>TAHUN PELAJARAN <?= ($bulan >= '07') ? "$tahun/" . ($tahun+1) : ($tahun-1) . "/$tahun" ?></h2>
    </div>

    <div class="content-layout">
        
        <div class="table-area">
            <table class="rekap-table">
                <thead>
                    <tr>
                        <th width="20%">No</th>
                        <th width="40%">Tgl.</th>
                        <th width="40%">Jumlah Peminjaman</th>
                    </tr>
                </thead>
                <tbody>
                    <?php for ($t = 1; $t <= 31; $t++): ?>
                    <tr>
                        <td><?= $t ?></td>
                        <td><?= $t ?></td>
                        <td><?= ($t <= $jumlah_hari) ? $data_rekap[$t] : '-' ?></td>
                    </tr>
                    <?php endfor; ?>
                    <tr class="fw-bold" style="background-color: #f2f2f2;">
                        <td colspan="2">JUMLAH</td>
                        <td><?= $total_seluruh_peminjaman ?></td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div class="chart-area">
            <div class="chart-title">
                Grafik Data Peminjaman Buku Bulanan<br>
                Bulan <?= $bulan_tampil ?> <?= $tahun ?>
            </div>
            
            <canvas id="chartPrintBulanan" style="width: 100%; height: 420px;"></canvas>
        </div>

    </div>

    <div class="ttd-layout">
        <div class="ttd-box">
            Mengetahui,<br>
            Kepala Perpustakaan
            <div class="ttd-space"></div>
            <span class="fw-bold" style="text-decoration: underline;">Tutik Muryani, S.Pd</span><br>
            NIP. 198303032022212025
        </div>
        
        <div class="ttd-box">
            Kismantoro, <?= date('d') ?> <?= $nama_bulan[date('m')] ?> <?= date('Y') ?><br>
            Petugas Perpustakaan
            <div class="ttd-space"></div>
            <span class="fw-bold" style="text-decoration: underline;">Ari Okta Wulandari, A.Ma.Pust</span>
        </div>
    </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener("DOMContentLoaded", function() {
        const ctx = document.getElementById('chartPrintBulanan').getContext('2d');
        
        const totalHari = <?= $jumlah_hari ?>;
        const labelsData = [];
        for(let i = 1; i <= 31; i++) {
            labelsData.push(i);
        }

        const rawValues = <?= json_encode(array_values($data_rekap)); ?>;

        // Memetakan warna selang-seling (Teal & Merah Muda) agar semirip mungkin dengan print out fisik asli sekolah
        const barColors = rawValues.map((val, idx) => {
            return idx % 2 === 0 ? '#319795' : '#E53E3E';
        });

        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: labelsData,
                datasets: [{
                    data: rawValues,
                    backgroundColor: barColors,
                    borderWidth: 1,
                    borderColor: '#4A5568'
                }]
            },
            options: {
                responsive: false,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        min: 0,
                        max: 5, // Batas skala atas sesuai gambar lembar fisik (0 - 4.5)
                        ticks: {
                            stepSize: 0.5, // Lonjakan ticks pecahan 0,5 angka sesuai lembar fisik sekolah
                            color: '#000',
                            font: { size: 10 }
                        },
                        grid: { color: '#E2E8F0' }
                    },
                    x: {
                        ticks: {
                            color: '#000',
                            font: { size: 9, weight: 'bold' }
                        },
                        grid: { display: false }
                    }
                }
            }
        });

        // TRIGGER AUTO-PRINT: Berjalan otomatis sesaat setelah grafik selesai dirender browser
        setTimeout(function() {
            window.print();
        }, 800);
    });
</script>

</body>
</html>