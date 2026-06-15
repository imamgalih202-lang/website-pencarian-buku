<?php
require_once '../config/koneksi.php';

$pesan = "";
$nama_siswa = "";

if (isset($_POST['nis'])) {
    $nis = mysqli_real_escape_string($mysqli, $_POST['nis']);
    
    // 1. Cek apakah NIS ini ada di database siswa?
    $cek = $mysqli->query("SELECT nama FROM siswa WHERE nis = '$nis'");
    
    if ($cek->num_rows > 0) {
        $data = $cek->fetch_assoc();
        $nama_siswa = $data['nama'];
        
        // 2. MASUKKAN OTOMATIS KE DAFTAR PENGUNJUNG
        $mysqli->query("INSERT INTO pengunjung (nis, tgl_kunjung) VALUES ('$nis', NOW())");
        $pesan = "success";
    } else {
        $pesan = "error";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Scanner Pintu Masuk</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color: #1a1a1a; color: white; display: flex; justify-content: center; align-items: center; height: 100vh; margin: 0; }
        .scan-area { text-align: center; border: 5px dashed #333; padding: 50px; border-radius: 30px; width: 80%; }
        .input-hidden { position: absolute; opacity: 0; } /* Biar ga kelihatan tapi tetep nangkep input scanner */
    </style>
</head>
<body onclick="document.getElementById('focus_node').focus();">

    <div class="scan-area">
        <?php if ($pesan == "success"): ?>
            <h1 class="display-1 text-success">✅</h1>
            <h2 class="fw-bold">SELAMAT DATANG</h2>
            <h1 class="text-warning"><?= strtoupper($nama_siswa) ?></h1>
            <script>setTimeout(() => { window.location.href='gate_scanner.php'; }, 2000);</script>
        <?php elseif ($pesan == "error"): ?>
            <h1 class="display-1 text-danger">❌</h1>
            <h2 class="text-danger">KARTU TIDAK TERDAFTAR!</h2>
            <script>setTimeout(() => { window.location.href='gate_scanner.php'; }, 2000);</script>
        <?php else: ?>
            <img src="../assets/img/scan-icon.png" width="150" class="mb-4">
            <h2 class="fw-bold">SILAKAN SCAN KTS ANDA</h2>
            <p class="text-muted">Tempelkan barcode pada scanner fisik</p>
        <?php endif; ?>

        <form method="POST" id="scanForm">
            <input type="text" name="nis" id="focus_node" class="input-hidden" autofocus autocomplete="off">
        </form>
    </div>

    <script>
        // Pastikan kursor selalu fokus di input meski admin ngeklik layar
        setInterval(() => { document.getElementById('focus_node').focus(); }, 100);
    </script>
</body>
</html>