<?php
// ==========================================
// PENGATURAN DATABASE
// ==========================================
$host     = "localhost";
$username = "root";       // Default XAMPP username
$password = "";           // Default XAMPP password (kosong)
$database = "websiteskripsi"; // Nama database baru yang baru saja kita buat

// Membuat koneksi menggunakan MySQLi (Object-Oriented)
$mysqli = new mysqli($host, $username, $password, $database);

// Mengecek apakah koneksi berhasil atau gagal
if ($mysqli->connect_error) {
    die("Koneksi database gagal: " . $mysqli->connect_error);
}

// Mengatur charset utf8mb4 agar mendukung karakter khusus/emoji dengan aman
$mysqli->set_charset("utf8mb4");


// ==========================================
// PENGATURAN BASE_URL (ALAMAT ABSOLUT)
// ==========================================
// Pastikan nama folder "websiteskripsi" di bawah ini persis 
// dengan nama folder proyek Anda di dalam C:\xampp\htdocs\
if (!defined('BASE_URL')) {
    define('BASE_URL', 'http://localhost/websiteskripsi/');
}
?>