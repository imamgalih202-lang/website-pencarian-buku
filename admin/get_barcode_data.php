<?php
// admin/get_barcode_data.php
require_once '../config/koneksi.php';

// Memastikan respons dalam format JSON agar mudah dibaca JavaScript
header('Content-Type: application/json');

$response = [
    'status' => 'error',
    'message' => 'Permintaan tidak valid.',
    'data' => null
];

// Menangkap jenis permintaan (apakah scan siswa atau buku)
if (isset($_GET['type']) && isset($_GET['barcode'])) {
    $type = $_GET['type'];
    $barcode = $mysqli->real_escape_string($_GET['barcode']);

    if ($type === 'siswa') {
        // Cari data siswa berdasarkan barcode KTS
        $query = $mysqli->query("SELECT * FROM siswa WHERE barcode_siswa = '$barcode'");
        if ($query && $query->num_rows > 0) {
            $siswa = $query->fetch_assoc();
            $response = [
                'status' => 'success',
                'message' => 'Siswa ditemukan',
                'data' => [
                    'nama_siswa' => $siswa['nama_siswa'],
                    'kelas' => $siswa['kelas']
                ]
            ];
        } else {
            $response['message'] = 'KTS Tidak Dikenali!';
        }
    } 
    elseif ($type === 'buku') {
        // Cari data buku berdasarkan barcode buku/ISBN
        $query = $mysqli->query("SELECT id, title, rak FROM books WHERE barcode_buku = '$barcode'");
        if ($query && $query->num_rows > 0) {
            $buku = $query->fetch_assoc();
            $response = [
                'status' => 'success',
                'message' => 'Buku ditemukan',
                'data' => [
                    'id_buku' => $buku['id'],
                    'title' => $buku['title'],
                    'rak' => $buku['rak']
                ]
            ];
        } else {
            $response['message'] = 'Barcode Buku Tidak Dikenali!';
        }
    }
}

// Kirim jawaban kembali ke browser
echo json_encode($response);
exit;