<?php
require_once 'cek_sesi.php'; // Memastikan sesi login aman
require_once '../config/koneksi.php';

if (isset($_POST['barcode']) && isset($_POST['nama'])) {
    $barcode = mysqli_real_escape_string($mysqli, $_POST['barcode']);
    $nama = mysqli_real_escape_string($mysqli, $_POST['nama']);

    // Cocokkan data sirkulasi berdasarkan kombinasi barcode buku dan nama peminjam
    $query = $mysqli->query("
        SELECT p.id, b.title, p.nama_peminjam, p.kelas, p.tanggal_kembali 
        FROM peminjaman p 
        JOIN books b ON p.id_buku = b.id 
        WHERE b.barcode_buku = '$barcode' AND p.nama_peminjam = '$nama' AND p.status = 'Dipinjam'
        LIMIT 1
    ");

    if ($query->num_rows > 0) {
        $data = $query->fetch_assoc();
        
        // =====================================================================
        // PERBAIKAN LOGIKA: MENGGUNAKAN SATUAN WAKTU JAM & MENIT SECARA PENUH
        // =====================================================================
        $tgl_tempo = new DateTime($data['tanggal_kembali']); // Memuat tanggal + jam tenggat dari DB
        $tgl_sekarang = new DateTime(date('Y-m-d H:i:s'));  // Memuat waktu laptop detik ini lengkap
        
        $denda = 0; 
        $menit_terlambat = 0;

        if ($tgl_sekarang > $tgl_tempo) {
            $interval = $tgl_sekarang->diff($tgl_tempo);
            
            // Konversi selisih objek waktu (hari, jam, menit) menjadi total MENIT murni
            $menit_terlambat = ($interval->days * 24 * 60) + ($interval->h * 60) + $interval->i;
            
            if ($menit_terlambat > 0) {
                $tarif_denda = 1000; // Tarif simulasi: Rp 1.000 / menit
                $denda = $menit_terlambat * $tarif_denda;
            }
        }
        // =====================================================================

        echo json_encode([
            'status' => 'success', 
            'id_pinjam' => $data['id'], 
            'judul' => $data['title'], 
            'nama' => $data['nama_peminjam'], 
            'kelas' => $data['kelas'], 
            'hari' => $menit_terlambat, // Mengirimkan total hitungan menit ke interface depan
            'denda' => $denda
        ]);
    } else {
        echo json_encode([
            'status' => 'error', 
            'msg' => 'Data sirkulasi tidak cocok! Buku ini tidak tercatat dipinjam oleh siswa yang bersangkutan.'
        ]);
    }
}
?>