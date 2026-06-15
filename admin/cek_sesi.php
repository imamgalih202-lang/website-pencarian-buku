<?php
// admin/cek_sesi.php
session_start();

// Mengecek apakah pengunjung sudah login dan memiliki role 'admin'
if (!isset($_SESSION['status_login']) || $_SESSION['status_login'] !== 'admin') {
    // Jika belum login, tendang ke halaman login
    header("Location: ../auth/login.php?pesan=akses_ditolak");
    exit;
}
?>