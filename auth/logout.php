<?php
// Mulai sesi
session_start();

// Hapus semua variabel sesi
session_unset();

// Hancurkan sesi sepenuhnya
session_destroy();

// Arahkan kembali ke halaman login dengan pesan sukses logout
header("Location: login.php?pesan=logout");
exit;
?>