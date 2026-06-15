<?php
require_once '../config/koneksi.php';

if (isset($_POST['nama'])) {
    $nama = mysqli_real_escape_string($mysqli, $_POST['nama']);

    // Mencari di tabel peminjaman menggunakan nama_peminjam yang aktif
    $query = $mysqli->query("SELECT nama_peminjam FROM peminjaman WHERE nama_peminjam = '$nama' AND status = 'Dipinjam' LIMIT 1");

    if ($query->num_rows > 0) {
        echo json_encode([
            'status' => 'success', 
            'nama' => $query->fetch_assoc()['nama_peminjam']
        ]);
    } else {
        echo json_encode([
            'status' => 'error', 
            'msg' => 'Data tidak ditemukan atau siswa tidak memiliki tanggungan pinjaman buku!'
        ]);
    }
}
?>