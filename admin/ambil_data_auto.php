<?php
require_once 'cek_sesi.php';
require_once '../config/koneksi.php';

// Pastikan request data dikirim melalui method POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $type = isset($_POST['type']) ? $_POST['type'] : '';
    $code = isset($_POST['code']) ? $mysqli->real_escape_string($_POST['code']) : '';

    if (empty($code)) {
        echo json_encode(['status' => 'error', 'msg' => 'Kode tidak boleh kosong!']);
        exit;
    }

    // 1. LOGIKA UNTUK AUTO-FILL DATA SISWA VIA SCAN KTS
    if ($type === 'siswa') {
        // Menggunakan kolom asli database Anda: barcode_siswa dan nama_siswa
        $query = $mysqli->query("SELECT nama_siswa, kelas FROM siswa WHERE barcode_siswa = '$code' LIMIT 1");
        
        if ($query && $query->num_rows > 0) {
            $data_siswa = $query->fetch_assoc();
            echo json_encode([
                'status' => 'success',
                'data' => [
                    'nama_siswa' => $data_siswa['nama_siswa'],
                    'kelas' => $data_siswa['kelas']
                ]
            ]);
        } else {
            echo json_encode([
                'status' => 'error', 
                'msg' => 'Nomor KTS Siswa tidak terdaftar di database!'
            ]);
        }
        exit;
    }

    // 2. LOGIKA UNTUK AUTO-FILL DATA BUKU VIA SCAN BARCODE BUKU
    if ($type === 'buku') {
        // Mengambil data judul buku berdasarkan barcode_buku
        $query = $mysqli->query("SELECT title FROM books WHERE barcode_buku = '$code' LIMIT 1");
        
        if ($query && $query->num_rows > 0) {
            $data_buku = $query->fetch_assoc();
            echo json_encode([
                'status' => 'success',
                'data' => [
                    'title' => $data_buku['title']
                ]
            ]);
        } else {
            echo json_encode([
                'status' => 'error', 
                'msg' => 'Barcode Buku tidak dikenali di katalog!'
            ]);
        }
        exit;
    }

    // Jika parameter type tidak sesuai
    echo json_encode(['status' => 'error', 'msg' => 'Tipe request tidak valid!']);
    exit;
} else {
    // Memproteksi file agar tidak bisa ditembus langsung via URL browser
    header("HTTP/1.1 403 Forbidden");
    echo "Akses ditolak!";
    exit;
}