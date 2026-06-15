<?php
// admin/cetak_barcode_buku.php
require_once 'cek_sesi.php';
require_once '../config/koneksi.php';

// Ambil semua data buku yang memiliki barcode dari database
$query_buku = $mysqli->query("SELECT title, barcode_buku FROM books WHERE barcode_buku IS NOT NULL AND barcode_buku != '' ORDER BY id DESC");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Cetak Barcode Buku - Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <style>
        body { background-color: #f4f6f9; font-family: 'Poppins', sans-serif;}
        
        /* Desain Ukuran Stiker Barcode (6.5cm x 3.5cm) */
        .label-buku {
            width: 6.5cm;
            height: 3.5cm;
            background: #ffffff;
            border: 1px dashed #999; /* Garis putus-putus untuk panduan menggunting */
            padding: 8px;
            margin-bottom: 15px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            align-items: center;
            page-break-inside: avoid;
            background-color: white;
        }
        
        .label-header {
            font-size: 10px;
            font-weight: bold;
            text-align: center;
            margin-bottom: 3px;
            color: #B84E32;
        }
        
        .label-title {
            font-size: 11px;
            text-align: center;
            color: #333;
            font-weight: 600;
            /* Memotong teks jika judul buku terlalu panjang */
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            width: 100%;
        }
        
        /* Area Barcode */
        .barcode-img {
            height: 45px;
            width: 90%;
            object-fit: contain;
        }
        
        /* Hilangkan elemen non-stiker saat dicetak */
        @media print {
            .no-print { display: none !important; }
            body { background: white; margin: 0; padding: 0; }
            .label-buku { border: 1px solid #ccc; /* Garis potong lebih tipis saat diprint */ }
        }
    </style>
</head>
<body>

<div class="container py-5">
    <div class="d-flex justify-content-between align-items-center mb-4 no-print">
        <h3 class="fw-bold"><i class="bi bi-upc-scan text-primary"></i> Cetak Label Barcode Buku</h3>
        <div>
            <a href="books.php" class="btn btn-outline-dark me-2">Kembali ke Katalog</a>
            <button onclick="window.print()" class="btn btn-primary fw-bold"><i class="bi bi-printer"></i> Print Barcode</button>
        </div>
    </div>

    <div class="alert alert-info no-print">
        <i class="bi bi-info-circle-fill"></i> Gunakan <b>Kertas Stiker (HVS Sticker)</b> berukuran A4 untuk mencetak halaman ini. Gunting sesuai garis putus-putus dan tempelkan pada sampul belakang buku yang tidak memiliki barcode bawaan.
    </div>

    <div class="row row-cols-2 row-cols-md-3 row-cols-lg-4 g-3 justify-content-center">
        <?php while($b = $query_buku->fetch_assoc()): ?>
        <div class="col d-flex justify-content-center">
            
            <div class="label-buku">
                <div class="label-header">PERPUS SMKN 1 KISMANTORO</div>
                
                <div class="label-title" title="<?= htmlspecialchars($b['title']) ?>">
                    <?= htmlspecialchars($b['title']) ?>
                </div>
                
                <img src="https://barcode.tec-it.com/barcode.ashx?data=<?= urlencode($b['barcode_buku']) ?>&code=Code128" alt="Barcode" class="barcode-img">
            </div>

        </div>
        <?php endwhile; ?>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>