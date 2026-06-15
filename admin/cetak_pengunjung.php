<?php
require_once 'cek_sesi.php';
require_once '../config/koneksi.php';

// Ambil parameter bulan dan tahun dari URL, jika tidak ada gunakan bulan & tahun aktif saat ini
$bulan = isset($_GET['bulan']) ? $mysqli->real_escape_string($_GET['bulan']) : date('m');
$tahun = isset($_GET['tahun']) ? $mysqli->real_escape_string($_GET['tahun']) : date('Y');

// Array nama bulan dalam bahasa Indonesia untuk judul kop laporan
$nama_bulan = [
    '01' => 'Januari', '02' => 'Februari', '03' => 'Maret', '04' => 'April',
    '05' => 'Mei', '06' => 'Juni', '07' => 'Juli', '08' => 'Agustus',
    '09' => 'September', '10' => 'Oktober', '11' => 'November', '12' => 'Desember'
];

$bulan_tampil = $nama_bulan[$bulan];

// Ambil data seluruh log kehadiran pengunjung perpustakaan berdasarkan bulan dan tahun pilihan
$sql_rekap = "SELECT * FROM pengunjung 
              WHERE MONTH(tgl_kunjung) = '$bulan' 
              AND YEAR(tgl_kunjung) = '$tahun' 
              ORDER BY tgl_kunjung ASC";
$query_rekap = $mysqli->query($sql_rekap);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Cetak Rekap Kehadiran Pengunjung Perpustakaan</title>
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
        
        /* Gaya Kop Surat Resmi Sekolah */
        .header-laporan {
            text-align: center;
            margin-bottom: 25px;
            line-height: 1.4;
            border-bottom: 2px solid #000;
            padding-bottom: 10px;
        }
        .header-laporan h2 {
            margin: 0;
            font-size: 16px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .header-laporan h3 {
            margin: 5px 0 0 0;
            font-size: 14px;
            font-weight: normal;
        }
        .header-laporan h4 {
            margin: 5px 0 0 0;
            font-size: 13px;
            font-weight: bold;
        }

        /* Desain Tabel Laporan */
        table.rekap-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 11px;
            margin-top: 15px;
        }
        table.rekap-table th, table.rekap-table td {
            border: 1px solid #000;
            padding: 6px 8px;
        }
        table.rekap-table th {
            background-color: #f2f2f2;
            font-weight: bold;
            text-transform: uppercase;
            text-align: center;
        }
        .text-center { text-align: center !important; }
        .fw-bold { font-weight: bold; }

        /* Tata Letak Tanda Tangan */
        .ttd-layout {
            display: flex;
            justify-content: space-between;
            margin-top: 50px;
            padding: 0 10px;
            font-size: 12px;
        }
        .ttd-box {
            width: 40%;
            text-align: center;
            line-height: 1.4;
        }
        .ttd-space {
            height: 75px;
        }

        /* Optimasi Kertas Cetak A4 / F4 */
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
        <h3>Laporan Rekapitulasi Daftar Kehadiran Siswa / Pengunjung</h3>
        <h4>Periode Bulan: <?= $bulan_tampil ?> <?= $tahun ?></h4>
    </div>

    <table class="rekap-table">
        <thead>
            <tr>
                <th width="5%">No</th>
                <th width="25%">Tanggal / Waktu Masuk</th>
                <th width="20%">NIS / Barcode</th>
                <th width="35%">Nama Lengkap Siswa</th>
                <th width="15%">Kelas</th>
            </tr>
        </thead>
        <tbody>
            <?php 
            $no = 1;
            if($query_rekap && $query_rekap->num_rows > 0):
                while($row = $query_rekap->fetch_assoc()):
                    $nis_scan = $row['nis'];
                    
                    // Kueri relasi untuk menarik nama dan kelas dari tabel siswa asli berdasarkan hasil scan
                    $cari_siswa = $mysqli->query("SELECT nama_siswa, kelas FROM siswa WHERE barcode_siswa = '$nis_scan' OR id_siswa = '$nis_scan' LIMIT 1");
                    if($cari_siswa && $cari_siswa->num_rows > 0) {
                        $data_s = $cari_siswa->fetch_assoc();
                        $nama_s = $data_s['nama_siswa'];
                        $kelas_s = $data_s['kelas'];
                    } else {
                        // Jalur penyelamat alternatif jika data siswa terhapus namun log pengunjung masih ada
                        $nama_s = !empty($row['nama']) ? $row['nama'] : 'Siswa Tanpa Nama';
                        $kelas_s = !empty($row['kelas']) ? $row['kelas'] : '-';
                    }
            ?>
            <tr>
                <td class="text-center"><?= $no++ ?></td>
                <td><?= date('d M Y', strtotime($row['tgl_kunjung'])) ?> - <?= date('H:i', strtotime($row['tgl_kunjung'])) ?> WIB</td>
                <td class="text-center"><code><?= htmlspecialchars($nis_scan) ?></code></td>
                <td><?= htmlspecialchars($nama_s) ?></td>
                <td class="text-center"><?= htmlspecialchars($kelas_s) ?></td>
            </tr>
            <?php 
                endwhile;
            else: 
            ?>
            <tr>
                <td colspan="5" class="text-center" style="padding: 30px; color: #666; font-style: italic;">
                    Tidak ada rekapan data kehadiran siswa pada periode bulan ini (<?= $bulan_tampil ?> <?= $tahun ?>).
                </td>
            </tr>
            <?php endif; ?>
        </tbody>
    </table>

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

<script>
    window.onload = function() {
        window.print();
    }
</script>

</body>
</html>